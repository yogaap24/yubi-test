<script setup>
import { reactive, watch, computed } from 'vue';
import { getNewSalesOrder, getNewSalesOrderItem } from '../models/salesOrder.js';
import { createSalesOrder } from '../services/apiService.js';
import { customers, products, currencies, orderTypes, statuses, units } from '../data/static.js';

const form = reactive(getNewSalesOrder());

const selectedProductIds = computed(() => {
  return new Set(
    form.items
      .map(item => item.product_uuid)
      .filter(uuid => uuid)
  );
});

const getAvailableProducts = (currentItem) => {
  return products.filter(product => {
    const isNotSelected = !selectedProductIds.value.has(product.id);
    const isCurrentItem = product.id === currentItem.product_uuid;
    
    return isNotSelected || isCurrentItem;
  });
};

const addItem = () => form.items.push(getNewSalesOrderItem());

const removeItem = (index) => form.items.splice(index, 1);

const onProductSelect = (item) => {
  const product = products.find(p => p.id === item.product_uuid);
  if (product) {
    item.price_sell = product.price_sell;
    item.price_buy = product.price_buy;
    item.item_unit_id = product.unit_id;
    item.item_id = Math.floor(Math.random() * 1000);
    const unit = units.find(u => u.id === product.unit_id);
    item.product_name = product.name;
    item.product_code = product.code;
    item.unit_name = unit ? unit.name : '';
  }
};

const minShippingDate = computed(() => {
  return form.order_at;
});

watch(() => form.order_at, (newOrderDate) => {
  if (form.shipping_at && newOrderDate > form.shipping_at) {
    form.shipping_at = newOrderDate;
  }
});

const submitForm = async () => {
  const payload = JSON.parse(JSON.stringify(form));

  payload.items.forEach(item => {
    delete item.product_name;
    delete item.product_code;
    delete item.unit_name;
    if (item.disc_perc > 0) {
      item.disc_am = null;
    } else if (item.disc_am > 0) {
      item.disc_perc = null;
    } else {
      item.disc_perc = null;
      item.disc_am = null;
    }
  });

  try {
    const result = await createSalesOrder(payload);
    alert('Sukses! Order berhasil dibuat:', result);
    Object.assign(form, getNewSalesOrder());
  } catch (error) {
    alert('Gagal menyimpan data');
  }
};

watch(() => form.items, (newItems) => {
  let subtotal = 0;
  let totalDiscount = 0;
  newItems.forEach(item => {
    const lineTotal = item.qty * item.price_sell;
    const discount = item.disc_am > 0 ? item.disc_am : (lineTotal * (item.disc_perc / 100));
    item.total_am = lineTotal - discount;
    subtotal += lineTotal;
    totalDiscount += discount;
  });
  form.subtotal = subtotal;
  form.total_discount = totalDiscount;
  form.grand_total = subtotal - totalDiscount;
}, { deep: true });

const handleDiscountChange = (item, type) => {
  if (type === 'amount' && item.disc_am > 0) {
    item.disc_perc = 0;
  } else if (type === 'percentage' && item.disc_perc > 0) {
    item.disc_am = 0;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
          <span>Sales</span>
          <span>></span>
          <span>Sales Orders</span>
          <span>></span>
          <span class="font-medium text-gray-900">Create</span>
        </div>
        <div class="flex space-x-3">
          <button class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Clear</button>
          <button @click="submitForm" class="px-4 py-2 text-sm bg-amber-600 text-white rounded-md hover:bg-amber-700">Create New</button>
        </div>
      </div>
    </div>

    <div class="p-6">
      <!-- Main Form Card -->
      <div class="bg-white rounded-lg shadow-sm border">
        <!-- Basic Information Header -->
        <div class="px-6 py-4 border-b bg-gray-50">
          <h2 class="text-lg font-medium text-gray-900 flex items-center">
            <span class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center mr-3 text-amber-700 text-xs font-bold">i</span>
            Basic Information
          </h2>
        </div>

        <!-- Form Fields -->
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- PO Buyer No -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">PO Buyer No</label>
              <input 
                type="text" 
                v-model="form.po_buyer_no" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <!-- Order Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Order Type</label>
              <select 
                v-model.number="form.order_type_id" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option :value="null" disabled>-- Select --</option>
                <option v-for="opt in orderTypes" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
              </select>
            </div>

            <!-- Customer -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
              <select 
                v-model.number="form.customer_id" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option :value="null" disabled>-- Select --</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option>
              </select>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="form.status" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option v-for="status in statuses" :key="status.id" :value="status.name">{{ status.name }}</option>
              </select>
            </div>

            <!-- Order Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Order Date</label>
              <input 
                type="date" 
                v-model="form.order_at" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <!-- Shipping Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Date</label>
              <input 
                type="date" 
                v-model="form.shipping_at" 
                :min="minShippingDate"
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <!-- Currency -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
              <select 
                v-model.number="form.currency_id" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option :value="null" disabled>-- Select --</option>
                <option v-for="currency in currencies" :key="currency.id" :value="currency.id">{{ currency.name }}</option>
              </select>
            </div>

            <!-- Exchange Rate -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Exchange Rate</label>
              <input 
                type="number"
                min="0" 
                v-model="form.exchange_rate" 
                step="0.1"
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
          </div>

          <!-- Buyer Address -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buyer Address</label>
            <textarea 
              rows="3"
              v-model="form.ship_dest"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              placeholder="Enter buyer address..."
            ></textarea>
          </div>

          <!-- Summary Boxes -->
          <div class="grid grid-cols-5 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="text-xs text-gray-500 mb-1">Sub Amount</div>
              <div class="text-lg font-semibold text-right">{{ form.subtotal.toLocaleString('id-ID') }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="text-xs text-gray-500 mb-1">Total Discount</div>
              <div class="text-lg font-semibold text-right">{{ form.total_discount.toLocaleString('id-ID') }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="text-xs text-gray-500 mb-1">After Discount</div>
              <div class="text-lg font-semibold text-right">{{ (form.subtotal - form.total_discount).toLocaleString('id-ID') }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="text-xs text-gray-500 mb-1">Total VAT</div>
              <div class="text-lg font-semibold text-right">0.00</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-md border border-blue-200">
              <div class="text-xs text-blue-700 mb-1">Grand Total</div>
              <div class="text-lg font-bold text-blue-700 text-right">{{ form.grand_total.toLocaleString('id-ID') }}</div>
            </div>
          </div>
        </div>

        <!-- Items Section -->
        <div class="border-t">
          <!-- Tab Header -->
          <div class="flex border-b bg-gray-50">
            <button class="px-6 py-3 text-sm font-medium text-gray-900 border-b-2 border-blue-500 bg-white">
              ITEMS
            </button>
            <button class="px-6 py-3 text-sm font-medium text-gray-500">
              REMARK
            </button>
            <button class="px-6 py-3 text-sm font-medium text-gray-500">
              SCHEDULE
            </button>
            <button class="px-6 py-3 text-sm font-medium text-gray-500">
              ATTACHMENTS
            </button>
          </div>

          <!-- Items Table -->
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex space-x-4">
                <button class="flex items-center px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                  <span class="mr-2">📦</span>
                  Ms. Product ({{ form.items.length }})
                </button>
                <button class="flex items-center px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                  <span class="mr-2">📄</span>
                  Quotation (0)
                </button>
              </div>
              <button class="text-sm text-blue-600 hover:text-blue-700">
                🔄 Clear References
              </button>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-50 border-y">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref Num</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disc (%)</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disc Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remark</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="!form.items.length">
                    <td colspan="13" class="px-4 py-8 text-center text-gray-500 text-sm">
                      -- No items added --
                    </td>
                  </tr>
                  <tr v-for="(item, index) in form.items" :key="index" class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">Products</td>
                    <td class="px-4 py-3 text-sm">-</td>
                    <td class="px-4 py-3 text-sm">Product</td>
                    <td class="px-4 py-3 text-sm">{{ item.product_code || '-' }}</td>
                    <td class="px-4 py-3">
                      <select 
                        v-model="item.product_uuid" 
                        @change="onProductSelect(item)" 
                        class="w-full max-w-xs px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                        <option :value="null" disabled>-- Select Product --</option>
                        <option v-for="p in getAvailableProducts(item)" :key="p.id" :value="p.id">{{ p.name }}</option>
                      </select>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ item.unit_name || '-' }}</td>
                    <td class="px-4 py-3">
                      <input 
                        type="number"
                        min="1"
                        v-model="item.price_sell" 
                        class="w-24 px-2 py-1 text-sm border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                    </td>
                    <td class="px-4 py-3">
                      <input 
                        type="number" 
                        v-model="item.qty"
                        min="1" 
                        class="w-20 px-2 py-1 text-sm border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                    </td>
                    <td class="px-4 py-3">
                      <input 
                        type="number" 
                        min="0"
                        v-model="item.disc_perc" 
                        @input="handleDiscountChange(item, 'percentage')"
                        class="w-20 px-2 py-1 text-sm border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        step="0.1"
                      >
                    </td>
                    <td class="px-4 py-3">
                      <input 
                        type="number" 
                        min="0"
                        v-model="item.disc_am" 
                        @input="handleDiscountChange(item, 'amount')"
                        class="w-24 px-2 py-1 text-sm border border-gray-300 rounded text-right focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-medium">
                      {{ item.total_am.toLocaleString('id-ID') }}
                    </td>
                    <td class="px-4 py-3">
                      <input 
                        type="text" 
                        v-model="item.remark"
                        placeholder="Remark" 
                        class="w-24 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      >
                    </td>
                    <td class="px-4 py-3 text-center">
                      <button 
                        @click="removeItem(index)" 
                        class="text-red-500 hover:text-red-700 font-medium"
                      >
                        ✖
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <button 
              @click="addItem" 
              class="mt-4 px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 flex items-center"
            >
              + Add Item
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

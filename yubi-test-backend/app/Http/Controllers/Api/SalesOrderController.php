<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesOrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'po_buyer_no'   => 'required|string|max:255|unique:sales_orders',
            'order_at'      => 'required|date_format:Y-m-d',
            'shipping_at'   => 'nullable|date_format:Y-m-d',
            'customer_id'   => 'required|integer',
            'order_type_id' => 'required|integer',
            'currency_id'   => 'required|integer',
            'ship_dest'     => 'required|string',
            'status'        => 'required|string|max:255',
            'exchange_rate' => 'required|numeric',
            'subtotal'      => 'required|numeric',
            'total_discount'=> 'required|numeric',
            'grand_total'   => 'required|numeric',
            'items'         => 'required|array|min:1',
            'items.*.product_uuid'  => 'required|uuid',
            'items.*.item_unit_id'  => 'required|integer',
            'items.*.item_id'       => 'required|integer',
            'items.*.ref_type'      => 'required|string',
            'items.*.remark'        => 'nullable|string',
            'items.*.qty'           => 'required|numeric',
            'items.*.price_sell'    => 'required|numeric',
            'items.*.price_buy'     => 'required|numeric',
            'items.*.disc_perc'     => 'nullable|numeric|prohibits:items.*.disc_am',
            'items.*.disc_am'       => 'nullable|numeric|prohibits:items.*.disc_perc',
            'items.*.total_am'      => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $order = DB::transaction(function () use ($request) {
                $salesOrder = SalesOrder::create($request->except('items'));
                $salesOrder->details()->createMany($request->items);
                return $salesOrder;
            });
            return response()->json($order->load('details'), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create sales order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
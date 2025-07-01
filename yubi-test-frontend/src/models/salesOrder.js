export const getNewSalesOrder = () => ({
    po_buyer_no: '',
    order_at: new Date().toISOString().slice(0, 10),
    shipping_at: null,
    ship_dest: '',
    customer_id: null,
    order_type_id: 1,
    currency_id: 1,
    status: 'OPEN',
    exchange_rate: 1.000,
    subtotal: 0,
    total_discount: 0,
    grand_total: 0,
    items: [],
});

export const getNewSalesOrderItem = () => ({
    product_uuid: null,
    item_unit_id: null,
    item_id: null,
    ref_type: 'Products',
    qty: 1,
    price_sell: 0,
    price_buy: 0,
    disc_perc: 0,
    disc_am: 0,
    total_am: 0,
    product_name: '',
    product_code: '',
    unit_name: ''
});
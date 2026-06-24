<?php

namespace App\Http\Requests\PurchaseOrderKitchen;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_order_kitchen_id' => 'required|string',
            'purchase_order_kitchen_date' => 'required|date',
            'purchase_order_kitchen_supplier_id' => 'required|string',
            'purchase_order_kitchen_to' => 'required|string',
            'purchase_order_kitchen_address' => 'required|string',
            'purchase_order_kitchen_note' => 'nullable|string',
            'purchase_order_kitchen_discount' => 'nullable|numeric|min:0',
            'purchase_order_kitchen_tax' => 'nullable|numeric|min:0',
            'purchase_order_kitchen_koefisien' => 'nullable|numeric|min:0',
            'purchase_order_kitchen_budget' => 'nullable|numeric|min:0',
            'purchase_order_kitchen_budget_over' => 'nullable|numeric|min:0',
            'yayasan_code' => 'nullable|string',
            'yayasan_name' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.purchase_order_kitchen_detail_itemid' => 'required|string',
            'detail.*.purchase_order_kitchen_detail_itemname' => 'required|string',
            'detail.*.purchase_order_kitchen_detail_formula' => 'nullable|numeric|min:0',
            'detail.*.purchase_order_kitchen_detail_qty' => 'required|numeric|min:0',
            'detail.*.purchase_order_kitchen_detail_qty_invoice' => 'nullable|numeric|min:0',
            'detail.*.purchase_order_kitchen_detail_uom' => 'required|string',
            'detail.*.purchase_order_kitchen_detail_last_price' => 'nullable|numeric|min:0',
            'detail.*.purchase_order_kitchen_detail_price' => 'required|numeric|min:0',
            'detail.*.purchase_order_kitchen_detail_send_date' => 'required|date',
        ];
    }
}

?>
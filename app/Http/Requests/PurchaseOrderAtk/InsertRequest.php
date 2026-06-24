<?php

namespace App\Http\Requests\PurchaseOrderAtk;

use App\Http\Requests\BaseRequest;

class InsertRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_order_atk_date' => 'required|date',
            'purchase_order_atk_supplier_id' => 'required|string',
            'purchase_order_atk_to' => 'required|string',
            'purchase_order_atk_address' => 'required|string',
            'purchase_order_atk_note' => 'nullable|string',
            'purchase_order_atk_discount' => 'nullable|numeric|min:0',
            'purchase_order_atk_tax' => 'nullable|numeric|min:0',
            'purchase_order_atk_koefisien' => 'nullable|numeric|min:0',
            'purchase_order_atk_budget' => 'nullable|numeric|min:0',
            'purchase_order_atk_budget_over' => 'nullable|numeric|min:0',
            'yayasan_code' => 'nullable|string',
            'yayasan_name' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.purchase_order_atk_detail_itemid' => 'required|string',
            'detail.*.purchase_order_atk_detail_itemname' => 'required|string',
            'detail.*.purchase_order_atk_detail_formula' => 'nullable|numeric|min:0',
            'detail.*.purchase_order_atk_detail_qty' => 'required|numeric|min:0',
            'detail.*.purchase_order_atk_detail_qty_invoice' => 'nullable|numeric|min:0',
            'detail.*.purchase_order_atk_detail_uom' => 'required|string',
            'detail.*.purchase_order_atk_detail_last_price' => 'nullable|numeric|min:0',
            'detail.*.purchase_order_atk_detail_price' => 'required|numeric|min:0',
            'detail.*.purchase_order_atk_detail_send_date' => 'required|date',
        ];
    }
}

?>

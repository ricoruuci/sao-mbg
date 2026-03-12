<?php

namespace App\Http\Requests\Invoice;

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
            'invoice_date' => 'required|date',
            'invoice_to' => 'required|string',
            'invoice_note' => 'nullable|string',
            'invoice_ppn' => 'required|numeric|min:0',
            'invoice_ppn_flag' => 'required|boolean',
            'company_id' => 'required|integer',
            'company_code' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.invoice_detail_description' => 'required|string',
            'detail.*.invoice_detail_qty' => 'required|numeric|min:1',
            'detail.*.invoice_detail_price' => 'required|numeric|min:0'
        ];
    }
}
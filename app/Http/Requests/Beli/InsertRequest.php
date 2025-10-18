<?php

namespace App\Http\Requests\Beli;

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
            'transdate' => 'required',
            'supplier_id' => 'required|string',
            'ppn' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'company_id' => 'required|integer',
            'company_code' => 'required|string',
            'detail' => 'nullable|array',
            'detail.*.bahan_baku_id' => 'required|string',
            'detail.*.qty' => 'required|numeric|min:0',
            'detail.*.price' => 'required|numeric|min:0',
            'detail.*.satuan' => 'required|string',
            'detailfoto' => 'nullable|array',
            'detailfoto.*.foto' => 'required|string'
        ];
    }
}

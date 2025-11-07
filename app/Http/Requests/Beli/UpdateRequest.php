<?php

namespace App\Http\Requests\Beli;

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
            'nota_beli' => 'required|string',
            'transdate' => 'required',
            'supplier_id' => 'required|string',
            'disc_amount' => 'required|numeric|min:0',
            'ppn' => 'required|numeric|min:0',
            'note' => 'nullable|string',
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

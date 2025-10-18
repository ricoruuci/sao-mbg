<?php

namespace App\Http\Requests\Issued;

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
            'tanggal' => 'required|string',
            'company_id' => 'required|integer',
            'detail' => 'nullable|array',
            'detail.*.bahan_baku_id' => 'required|string',
            'detail.*.jumlah' => 'required|numeric|min:0',
            'detail.*.satuan' => 'required|string',
        ];
    }
}

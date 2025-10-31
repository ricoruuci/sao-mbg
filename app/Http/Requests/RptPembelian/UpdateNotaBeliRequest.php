<?php

namespace App\Http\Requests\RptPembelian;

use App\Http\Requests\BaseRequest;

class UpdateNotaBeliRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'adjustment' => 'required|numeric',
            'data' => 'required|array',
            'data.*.nota_beli' => 'required|string',
        ];
    }
}

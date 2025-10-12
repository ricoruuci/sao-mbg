<?php

namespace App\Http\Requests\RptFinance;

use App\Http\Requests\BaseRequest;

class GetRequestBukuBesar extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dari' => 'required|date_format:Ymd',
            'sampai' => 'required|date_format:Ymd',
            'rekening_id' => 'nullable|string',
        ];
    }
}

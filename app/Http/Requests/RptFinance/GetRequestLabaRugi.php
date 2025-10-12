<?php

namespace App\Http\Requests\RptFinance;

use App\Http\Requests\BaseRequest;

class GetRequestLabaRugi extends BaseRequest
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
        ];
    }
}

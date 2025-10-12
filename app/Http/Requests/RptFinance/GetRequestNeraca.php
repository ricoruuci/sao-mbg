<?php

namespace App\Http\Requests\RptFinance;

use App\Http\Requests\BaseRequest;

class GetRequestNeraca extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'periode' => 'required|date_format:Ymd'
        ];
    }
}

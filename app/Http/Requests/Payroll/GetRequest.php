<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\BaseRequest;

class GetRequest extends BaseRequest
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
            'tx_id_keyword' => 'nullable|string',
            'tx_period_keyword' => 'nullable|string',
            'company_id' => 'nullable|integer',
        ];
    }
}

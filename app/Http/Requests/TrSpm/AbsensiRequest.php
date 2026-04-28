<?php

namespace App\Http\Requests\TrSpm;

use App\Http\Requests\BaseRequest;

class AbsensiRequest extends BaseRequest
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
            'company_id' => 'required|numeric',
        ];
    }
}

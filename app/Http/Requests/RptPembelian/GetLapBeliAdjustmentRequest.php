<?php

namespace App\Http\Requests\RptPembelian;

use App\Http\Requests\BaseRequest;

class GetLapBeliAdjustmentRequest extends BaseRequest
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
            'adjustment' => 'nullable|numeric',
            'company_id' => 'required|numeric',
        ];
    }
}

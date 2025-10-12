<?php

namespace App\Http\Requests\RptPenjualan;

use App\Http\Requests\BaseRequest;

class GetLapPenjualanHarianRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'transdate' => 'required|date_format:Ymd',
        ];
    }
}

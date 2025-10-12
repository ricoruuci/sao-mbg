<?php

namespace App\Http\Requests\RptPembelian;

use App\Http\Requests\BaseRequest;

class GetLapHutangRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'transdate' => 'required|date_format:Ymd',
            'search_keyword' => 'nullable|string',
            'supplier_keyword' => 'nullable|string',
        ];
    }
}

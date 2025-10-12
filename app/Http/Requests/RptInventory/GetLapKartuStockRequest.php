<?php

namespace App\Http\Requests\RptInventory;

use App\Http\Requests\BaseRequest;

class GetLapKartuStockRequest extends BaseRequest
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
            'search_keyword' => 'nullable|string'
        ];
    }
}

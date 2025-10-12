<?php

namespace App\Http\Requests\Beli;

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
            'search_keyword' => 'nullable|string',
            'supplier_keyword' => 'nullable|string'
        ];
    }
}

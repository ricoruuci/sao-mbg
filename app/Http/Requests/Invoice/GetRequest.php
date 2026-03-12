<?php

namespace App\Http\Requests\Invoice;

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
            'dari' => 'required|string', 
            'sampai' => 'required|string',
            'search_keyword' => 'nullable|string',
            'page' => 'nullable|integer|min:1'
        ];
    }
}
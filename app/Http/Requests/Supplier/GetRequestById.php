<?php

namespace App\Http\Requests\Supplier;

 use App\Http\Requests\BaseRequest;

class GetRequestById extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'supplier_id' => 'required|string',
        ];
    }
}

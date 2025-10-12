<?php

namespace App\Http\Requests\Supplier;

 use App\Http\Requests\BaseRequest;

class InsertRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'supplier_name' => 'required|string|max:30',
        ];
    }
}

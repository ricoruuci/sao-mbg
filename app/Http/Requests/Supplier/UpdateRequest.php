<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'supplier_id' => 'required|string',
            'supplier_name' => 'required|string|max:255',
        ];
    }
}

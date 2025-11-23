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
            'supplier_phone' => 'nullable|string|max:15',
            'supplier_pic' => 'nullable|string|max:50',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_holder' => 'nullable|string|max:100'
        ];
    }
}

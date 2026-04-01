<?php

namespace App\Http\Requests\Customer;

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
            'customer_id' => 'required|string',
            'customer_name' => 'required|string|max:100',
            'customer_contact_person' => 'nullable|string|max:100',
            'customer_city' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'customer_email' => 'nullable|email|max:100',
            'customer_npwp' => 'nullable|string|max:50',
            'customer_account_manager' => 'nullable|string|max:100',
            'customer_limit_piutang' => 'nullable|numeric',
            'customer_address' => 'nullable|string|max:255',
            'customer_address_npwp' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string|max:255',
            'customer_term' => 'nullable|integer|min:0',
        ];
    }
}

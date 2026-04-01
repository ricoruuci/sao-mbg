<?php

namespace App\Http\Requests\Customer;

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
            'customer_id' => 'required|string',
        ];
    }
}

<?php

namespace App\Http\Requests\Invoice;

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
            'invoice_code' => 'required|string'
        ];
    }
}
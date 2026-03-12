<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\BaseRequest;

class DeleteRequest extends BaseRequest
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
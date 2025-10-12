<?php

namespace App\Http\Requests\Jual;

use App\Http\Requests\BaseRequest;

class UpdatePaymentRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nota_jual' => 'required|string',
            'payment_type' => 'required|in:0,1,2,3',
            'bank_id'      => 'required_if:payment_type,0,1,2|string|nullable',
        ];
    }
}

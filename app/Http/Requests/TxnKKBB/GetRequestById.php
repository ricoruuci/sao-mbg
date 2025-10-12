<?php

namespace App\Http\Requests\TxnKKBB;

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
            'voucher_id' => 'required|string',
        ];
    }
}

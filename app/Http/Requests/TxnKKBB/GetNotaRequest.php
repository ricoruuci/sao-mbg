<?php

namespace App\Http\Requests\TxnKKBB;

use App\Http\Requests\BaseRequest;

class GetNotaRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'actor' => 'required|string',
            'transdate' => 'required|string'
        ];
    }
}

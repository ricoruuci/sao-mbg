<?php

namespace App\Http\Requests\Beli;

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
            'nota_beli' => 'required|string',
        ];
    }
}

<?php

namespace App\Http\Requests\Rekening;

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
            'rekening_id' => 'required|string',
        ];
    }
}

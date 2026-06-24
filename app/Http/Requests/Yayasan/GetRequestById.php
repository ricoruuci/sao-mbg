<?php

namespace App\Http\Requests\Yayasan;

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
            'yayasan_code' => 'required|string',
        ];
    }
}

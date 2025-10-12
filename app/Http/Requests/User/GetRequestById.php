<?php

namespace App\Http\Requests\User;

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
            'userid' => 'required|string',
        ];
    }
}

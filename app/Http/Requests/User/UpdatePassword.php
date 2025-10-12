<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UpdatePassword extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userid' => 'required|string',
            'old_password' => 'required|string|max:10',
            'new_password' => 'required|string|max:10'
        ];
    }
}

<?php

namespace App\Http\Requests\User;

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
            'userid' => 'required|string',
        ];
    }
}

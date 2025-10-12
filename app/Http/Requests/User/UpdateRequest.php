<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userid' => 'required|string',
            'company_id' => 'required|string',
            'group_user' => 'required|string|in:ADM,USR'
        ];
    }
}

<?php

namespace App\Http\Requests\Employee;

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
            'id' => 'required|numeric',
        ];
    }
}

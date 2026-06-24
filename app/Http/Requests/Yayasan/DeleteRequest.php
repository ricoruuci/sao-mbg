<?php

namespace App\Http\Requests\Yayasan;

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
            'yayasan_code' => 'required|string',
        ];
    }
}

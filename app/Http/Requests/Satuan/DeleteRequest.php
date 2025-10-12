<?php

namespace App\Http\Requests\Satuan;

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
            'satuan' => 'required|string',
        ];
    }
}

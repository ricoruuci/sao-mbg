<?php

namespace App\Http\Requests\Jual;

use App\Http\Requests\BaseRequest;

class UpdateBatalRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nota_jual' => 'required|string',
        ];
    }
}

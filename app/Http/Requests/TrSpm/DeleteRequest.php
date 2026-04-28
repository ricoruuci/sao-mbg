<?php

namespace App\Http\Requests\TrSpm;

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
            'trspm_hd_code' => 'required|string|max:50',
        ];
    }
}

<?php

namespace App\Http\Requests\TrAbsensi;

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
            'tr_absensi_header_code' => 'required|string|max:50',
        ];
    }
}
<?php

namespace App\Http\Requests\Yayasan;

use App\Http\Requests\BaseRequest;

class InsertRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'yayasan_name' => 'required|string|max:100',
            'yayasan_address' => 'nullable|string|max:255',
            'yayasan_note' => 'nullable|string|max:255',
            'yayasan_phone' => 'nullable|string|max:30',
            'yayasan_email' => 'nullable|email|max:100',
        ];
    }
}

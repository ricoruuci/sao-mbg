<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userid' => 'required',
            'oldpassword' => 'required',
            'newpassword' => 'required',
            'confirmpassword' => 'required'
        ];
    }
}
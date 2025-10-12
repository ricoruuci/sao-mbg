<?php

namespace App\Http\Requests\Bank;

use Illuminate\Foundation\Http\FormRequest;

class InsertRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bank_id' => 'required|string',
            'bank_name' => 'required|string|max:255',
            'fgactive' => 'required|string|in:Y,T',
            'note' => 'nullable|string|max:500',
            'rekening_id' => 'required|string|max:50',
        ];
    }
}

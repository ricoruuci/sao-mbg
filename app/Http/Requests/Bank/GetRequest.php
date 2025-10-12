<?php

namespace App\Http\Requests\Bank;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fgactive' => 'nullable|string|in:Y,T,all',
            'search_keyword' => 'nullable|string',
        ];
    }
}

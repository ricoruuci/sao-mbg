<?php

namespace App\Http\Requests\TrAbsensi;

use App\Http\Requests\BaseRequest;

class GetRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search_keyword' => 'nullable|string|max:255',
        ];
    }
}
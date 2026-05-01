<?php

namespace App\Http\Requests\BahanBaku;

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
            'search_keyword' => 'nullable|string',
            'fg_active' => 'nullable|string|in:Y,T',
        ];
    }
}

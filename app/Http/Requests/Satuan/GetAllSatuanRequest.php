<?php

namespace App\Http\Requests\Satuan;

use App\Http\Requests\BaseRequest;

class GetAllSatuanRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search_keyword' => 'nullable|string',
        ];
    }
}

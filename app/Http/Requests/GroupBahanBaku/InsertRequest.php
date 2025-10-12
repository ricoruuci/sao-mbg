<?php

namespace App\Http\Requests\GroupBahanBaku;

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
            'group_bahan_baku_name' => 'required|string|max:255',
        ];
    }
}

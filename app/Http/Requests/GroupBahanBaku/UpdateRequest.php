<?php

namespace App\Http\Requests\GroupBahanBaku;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'group_bahan_baku_id' => 'required|string',
            'group_bahan_baku_name' => 'required|string|max:255',
        ];
    }
}

<?php

namespace App\Http\Requests\BahanBaku;

use App\Http\Requests\BaseRequest;

class GetRequestById extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bahan_baku_id' => 'required|string',
        ];
    }
}

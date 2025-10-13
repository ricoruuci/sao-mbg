<?php

namespace App\Http\Requests\BahanBaku;

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
            'bahan_baku_name' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'satuan_besar' => 'required|string|max:50',
            'konversi' => 'required|numeric',
            'group_bahan_baku_id' => 'required|string',
        ];
    }
}

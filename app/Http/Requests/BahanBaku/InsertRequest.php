<?php

namespace App\Http\Requests\BahanBaku;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Validator;

class InsertRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bahan_baku_name' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'satuan_besar' => 'required|string|max:20',
            'konversi' => 'required|numeric|min:1',
            'group_bahan_baku_id' => 'required|string|max:20',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->satuan === $this->satuan_besar && $this->konversi != 1) {
                $validator->errors()->add('konversi', 'Konversi harus bernilai 1 jika satuan kecil sama dengan satuan besar.');
            }
        });
    }
}

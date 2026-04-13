<?php

namespace App\Http\Requests\Rekening;

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
            'rekening_id' => 'required|string',
            'rekening_name' => 'required|string|max:100',
            'note' => 'nullable|string|max:200',
            'group_rek_id' => 'required|string|max:20',
        ];
    }
}

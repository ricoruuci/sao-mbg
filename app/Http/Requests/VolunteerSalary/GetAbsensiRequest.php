<?php

namespace App\Http\Requests\VolunteerSalary;

use App\Http\Requests\BaseRequest;

class GetAbsensiRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dari' => 'required|date_format:Y-m-d H:i:s',
            'sampai' => 'required|date_format:Y-m-d H:i:s',
            'company_id' => 'nullable|string',
        ];
    }
}

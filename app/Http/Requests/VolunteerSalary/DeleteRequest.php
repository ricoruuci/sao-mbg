<?php

namespace App\Http\Requests\VolunteerSalary;

use App\Http\Requests\BaseRequest;

class DeleteRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'volunteer_salary_code' => 'required|string|max:30',
        ];
    }
}

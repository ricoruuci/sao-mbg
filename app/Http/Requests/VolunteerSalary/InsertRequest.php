<?php

namespace App\Http\Requests\VolunteerSalary;

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
            'volunteer_salary_instansi' => 'required|string|max:255',
            'volunteer_salary_date' => 'required|date',
            'volunteer_salary_date_from' => 'required|date',
            'volunteer_salary_date_to' => 'required|date|after_or_equal:volunteer_salary_date_from',
            'volunteer_salary_name' => 'required|string|max:255',
            'volunteer_salary_position' => 'nullable|string|max:150',
            'volunteer_salary_price' => 'required|numeric|min:0',
            'volunteer_salary_qty' => 'required|numeric|min:0',
            'volunteer_salary_overtime' => 'nullable|numeric|min:0',
            'volunteer_salary_total' => 'nullable|numeric|min:0',
        ];
    }
}

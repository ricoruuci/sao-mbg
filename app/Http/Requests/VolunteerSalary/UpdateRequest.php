<?php

namespace App\Http\Requests\VolunteerSalary;

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
            'volunteer_salary_hd_code' => 'required|string|max:50',
            'volunteer_salary_hd_date' => 'required|date_format:Ymd',
            'volunteer_salary_hd_date_from' => 'required|date_format:Ymd',
            'volunteer_salary_hd_date_to' => 'required|date_format:Ymd|after_or_equal:volunteer_salary_hd_date_from',
            'volunteer_salary_hd_adjust' => 'nullable|numeric|min:0',
            'volunteer_salary_hd_subtotal' => 'nullable|numeric|min:0',
            'volunteer_salary_hd_subbonuses' => 'nullable|numeric|min:0',
            'volunteer_salary_hd_note' => 'nullable|string',

            'detail' => 'required|array|min:1',
            'detail.*.volunteer_salary_dt_user_code' => 'required|string|max:50',
            'detail.*.volunteer_salary_dt_user_name' => 'required|string|max:100',
            'detail.*.volunteer_salary_dt_divisi' => 'nullable|string|max:50',
            'detail.*.volunteer_salary_dt_work_day' => 'nullable|numeric|min:0',
            'detail.*.volunteer_salary_dt_price' => 'nullable|numeric|min:0',
            'detail.*.volunteer_salary_dt_bonuses' => 'nullable|numeric|min:0',
            'detail.*.volunteer_salary_dt_total' => 'nullable|numeric|min:0',
        ];
    }
}

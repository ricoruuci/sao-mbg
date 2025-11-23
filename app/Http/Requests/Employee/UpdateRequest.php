<?php

namespace App\Http\Requests\Employee;

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
            'id' => 'required|numeric',
            'employee_name' => 'required|string|max:100',
            'join_date' => 'required|date_format:Ymd',
            'fg_active' => 'required|in:Y,T',
            'meal_amount' => 'nullable|numeric|min:0',
            'salary_amount' => 'nullable|numeric|min:0',
            'other_amount' => 'nullable|numeric|min:0',
            'company_id' => 'required|numeric'
        ];
    }
}

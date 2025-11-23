<?php

namespace App\Http\Requests\Payroll;

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
            'id' => 'required|integer',
            'tx_date' => 'required',
            'tx_period' => 'required|string',
            'work_days' => 'required|numeric|min:0',
            'company_id' => 'required|integer',
            'detail' => 'nullable|array',
            'detail.*.employee_id' => 'required|integer',
            'detail.*.meal_amount' => 'required|numeric|min:0',
            'detail.*.salary_amount' => 'required|numeric|min:0',
            'detail.*.other_amount' => 'required|numeric|min:0',
            'detail.*.present_days' => 'required|numeric|min:0',
        ];
    }
}

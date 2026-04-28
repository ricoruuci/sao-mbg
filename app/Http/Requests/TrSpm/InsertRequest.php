<?php

namespace App\Http\Requests\TrSpm;

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
            'trspm_hd_date' => 'required|date_format:Y-m-d',
            'trspm_hd_date_from' => 'required|date_format:Y-m-d',
            'trspm_hd_date_to' => 'required|date_format:Y-m-d',
            'trspm_hd_company_id' => 'required|string|max:50',
            'trspm_hd_company_name' => 'nullable|string|max:100',
            'trspm_hd_work_days' => 'nullable|numeric|min:0',
            'trspm_hd_overtime_adjustment' => 'nullable|numeric|min:0',
            'trspm_hd_note' => 'nullable|string',
            
            'detail' => 'required|array|min:1',
            'detail.*.trspm_dt_user_code' => 'required|string|max:50',
            'detail.*.trspm_dt_user_name' => 'required|string|max:100',
            'detail.*.trspm_dt_divisi' => 'nullable|string|max:50',
            'detail.*.trspm_dt_work_day' => 'nullable|numeric|min:0',
            'detail.*.trspm_dt_price' => 'nullable|numeric|min:0',
            'detail.*.trspm_dt_bonuses' => 'nullable|numeric|min:0',
            'detail.*.trspm_dt_overtime' => 'nullable|numeric|min:0',
            'detail.*.trspm_dt_total' => 'nullable|numeric|min:0',
        ];
    }
}

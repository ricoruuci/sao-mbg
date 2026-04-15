<?php

namespace App\Http\Requests\TrAbsensi;

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
            'tr_absensi_header_code' => 'required|string|max:50',
            'tr_absensi_header_date' => 'required|date',
            'tr_absensi_header_name' => 'required|string|max:255',
            'tr_absensi_header_branch' => 'nullable|string|max:255',
            'replace_detail' => 'nullable|boolean',
            'expected_detail_count' => 'nullable|integer|min:1',
            'detail' => 'required|array|min:1',
            'detail.*.tr_absensi_dt_id' => 'nullable|string|max:50',
            'detail.*.tr_absensi_dt_name' => 'required|string|max:255',
            'detail.*.tr_absensi_dt_date' => 'nullable|date',
            'detail.*.tr_absensi_dt_clock_in' => 'nullable|date_format:H:i',
            'detail.*.tr_absensi_dt_clock_out' => 'nullable|date_format:H:i',
            'detail.*.tr_absensi_dt_nominal' => 'nullable|numeric|min:0',
        ];
    }
}
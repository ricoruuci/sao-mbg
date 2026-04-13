<?php

namespace App\Http\Requests\TxnKKBB;

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
            'transdate' => 'required|date_format:Ymd',
            'actor' => 'nullable|string|max:50|required_unless:flagkkbb,JU',
            'note' => 'nullable|string|max:100',
            'flagkkbb' => 'required|string|in:KM,KK,BM,BK,JU,APK,APB',
            'company_id' => 'required|integer',
            'company_code' => 'required|string',
            'bank_id' => 'nullable|required_if:flagkkbb,BM,BK,APB|string',
            'total' => 'required|numeric',
            'date_costing' => 'nullable|date_format:Ymd',
            'detail' => 'required|array',
            'detail.*.rekeningid' => 'required|string',
            'detail.*.note' => 'nullable|string|max:255',
            'detail.*.amount' => 'required|numeric',
            'detail.*.jenis' => 'required|string|in:D,K',
        ];
    }
}

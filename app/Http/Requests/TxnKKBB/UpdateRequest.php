<?php

namespace App\Http\Requests\TxnKKBB;

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
            'voucher_id' => 'required|string',
            'transdate' => 'required|date_format:Ymd',
            'actor' => 'nullable|string|max:255|required_unless:flagkkbb,JU',
            'note' => 'nullable|string|max:255',
            'flagkkbb' => 'required|string|in:KM,KK,BM,BK,JU,APK,APB',
            'bank_id' => 'nullable|required_if:flagkkbb,BM,BK,APB|string',
            'total' => 'required|numeric',
            'detail' => 'required|array',
            'detail.*.rekeningid' => 'required|string',
            'detail.*.note' => 'nullable|string|max:255',
            'detail.*.amount' => 'required|numeric',
            'detail.*.jenis' => 'required|string|in:D,K',
        ];
    }
}

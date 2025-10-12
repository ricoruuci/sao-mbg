<?php

namespace App\Http\Requests\Jual;

use App\Http\Requests\BaseRequest;

class GetDataMejaRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'transdate' => 'required|date_format:Ymd',
        ];
    }
}

<?php

namespace App\Http\Requests\Menu;

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
            'menu_id' => 'required|string',
        ];
    }
}

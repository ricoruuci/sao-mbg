<?php

namespace App\Http\Requests\GroupMenu;

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
            'group_menu_id' => 'required|string',
            'group_menu_name' => 'required|string|max:255',
        ];
    }
}

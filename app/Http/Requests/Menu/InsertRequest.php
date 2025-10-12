<?php

namespace App\Http\Requests\Menu;

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
            'menu_name' => 'required|string',
            'price' => 'required|numeric',
            'fg_item' => 'required|in:A,B',
            'group_menu_id' => 'required|string',
            'item_picture' => 'nullable|string',
            'detail' => 'nullable|array',
            'detail.*.bahan_baku_id' => 'required|string',
            'detail.*.qty' => 'required|numeric',
            'detail.*.satuan' => 'required|string',
        ];
    }
}

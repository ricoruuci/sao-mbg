<?php

namespace App\Http\Requests\PurchaseOrderKitchen;

use App\Http\Requests\BaseRequest;

class GetRequestById extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_order_kitchen_id' => 'required|string',
        ];
    }
}

?>
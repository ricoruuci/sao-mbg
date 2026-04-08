<?php

namespace App\Http\Requests\PurchaseOrderAtk;

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
            'purchase_order_atk_id' => 'required|string',
        ];
    }
}

?>

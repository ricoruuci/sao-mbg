<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class PurchaseOrderKitchenDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trpurchaseorderkitchendt';

    public $timestamps = false;

    public function insertData($params)
    {
        return DB::insert(
            "INSERT INTO trpurchaseorderkitchendt
            (purchase_order_kitchen_id, purchase_order_kitchen_detail_itemid, purchase_order_kitchen_detail_itemname,
            purchase_order_kitchen_detail_formula, purchase_order_kitchen_detail_qty, purchase_order_kitchen_detail_qty_invoice,
            purchase_order_kitchen_detail_uom, purchase_order_kitchen_detail_last_price, purchase_order_kitchen_detail_price,
            purchase_order_kitchen_detail_total, purchase_order_kitchen_detail_send_date, upddate, upduser)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, getdate(), ?)",
            [
                $params['purchase_order_kitchen_id'],
                $params['purchase_order_kitchen_detail_itemid'],
                $params['purchase_order_kitchen_detail_itemname'],
                $params['purchase_order_kitchen_detail_formula'],
                $params['purchase_order_kitchen_detail_qty'],
                $params['purchase_order_kitchen_detail_qty_invoice'],
                $params['purchase_order_kitchen_detail_uom'],
                $params['purchase_order_kitchen_detail_last_price'],
                $params['purchase_order_kitchen_detail_price'],
                $params['purchase_order_kitchen_detail_total'],
                $params['purchase_order_kitchen_detail_send_date'],
                $params['upduser'],
            ]
        );
    }

    public function deleteData($id)
    {
        return DB::delete(
            "DELETE FROM trpurchaseorderkitchendt WHERE purchase_order_kitchen_id = ?",
            [$id]
        );
    }

    public function getDataById($id)
    {
        return DB::select(
            "SELECT
            a.purchase_order_kitchen_id,
            a.purchase_order_kitchen_detail_itemid,
            a.purchase_order_kitchen_detail_itemname,
            a.purchase_order_kitchen_detail_formula,
            a.purchase_order_kitchen_detail_qty,
            a.purchase_order_kitchen_detail_qty_invoice,
            a.purchase_order_kitchen_detail_uom,
            a.purchase_order_kitchen_detail_last_price,
            a.purchase_order_kitchen_detail_price,
            a.purchase_order_kitchen_detail_total,
            a.purchase_order_kitchen_detail_send_date,
            a.upddate,
            a.upduser
            FROM trpurchaseorderkitchendt a
            WHERE a.purchase_order_kitchen_id = ?",
            [$id]
        );
    }
}

?>
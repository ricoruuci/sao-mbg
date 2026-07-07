<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class PurchaseOrderAtkDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trpurchaseorderatkdt';

    public $timestamps = false;

    public function insertData($params)
    {
        return DB::insert(
            "INSERT INTO trpurchaseorderatkdt
            (purchase_order_atk_id, purchase_order_atk_detail_itemid, purchase_order_atk_detail_itemname,
            purchase_order_atk_detail_formula, purchase_order_atk_detail_qty, purchase_order_atk_detail_qty_invoice,
            purchase_order_atk_detail_uom, purchase_order_atk_detail_last_price, purchase_order_atk_detail_price,
            purchase_order_atk_detail_total, purchase_order_atk_detail_send_date, upddate, upduser)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, getdate(), ?)",
            [
                $params['purchase_order_atk_id'],
                $params['purchase_order_atk_detail_itemid'],
                $params['purchase_order_atk_detail_itemname'],
                $params['purchase_order_atk_detail_formula'],
                $params['purchase_order_atk_detail_qty'],
                $params['purchase_order_atk_detail_qty_invoice'],
                $params['purchase_order_atk_detail_uom'],
                $params['purchase_order_atk_detail_last_price'],
                $params['purchase_order_atk_detail_price'],
                $params['purchase_order_atk_detail_total'],
                $params['purchase_order_atk_detail_send_date'],
                $params['upduser'],
            ]
        );
    }

    public function deleteData($id)
    {
        return DB::delete(
            "DELETE FROM trpurchaseorderatkdt WHERE purchase_order_atk_id = ?",
            [$id]
        );
    }

    public function getDataById($id)
    {
        return DB::select(
            "SELECT
            a.purchase_order_atk_id,
            a.purchase_order_atk_detail_itemid AS itemid,
            a.purchase_order_atk_detail_itemname AS itemname,
            a.purchase_order_atk_detail_qty_invoice AS qty,
            a.purchase_order_atk_detail_uom AS satuan,
            a.purchase_order_atk_detail_price AS price,
            ISNULL(a.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(a.purchase_order_atk_detail_price, 0) AS total,
            a.purchase_order_atk_detail_send_date AS senddate,
            ISNULL(a.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(a.purchase_order_atk_detail_price, 0) AS subtotal,
            a.upddate,
            a.upduser
            FROM trpurchaseorderatkdt a
            WHERE a.purchase_order_atk_id = ?",
            [$id]
        );
    }
}

?>

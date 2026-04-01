<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class PurchaseOrderKitchenHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trpurchaseorderkitchenhd';

    public $timestamps = false;

    public function insertData($params)
    {
        return DB::insert(
            "INSERT INTO trpurchaseorderkitchenhd
            (purchase_order_kitchen_id, purchase_order_kitchen_date, purchase_order_kitchen_supplier_id,
            purchase_order_kitchen_supplier_name, purchase_order_kitchen_pic_name, purchase_order_kitchen_pic_phone,
            purchase_order_kitchen_address, purchase_order_kitchen_discount, purchase_order_kitchen_tax,
            purchase_order_kitchen_tax_amount, purchase_order_kitchen_subtotal, purchase_order_kitchen_grandtotal,
            upddate, upduser)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, getdate(), ?)",
            [
                $params['purchase_order_kitchen_id'],
                $params['purchase_order_kitchen_date'],
                $params['purchase_order_kitchen_supplier_id'],
                $params['purchase_order_kitchen_supplier_name'],
                $params['purchase_order_kitchen_pic_name'],
                $params['purchase_order_kitchen_pic_phone'],
                $params['purchase_order_kitchen_address'],
                $params['purchase_order_kitchen_discount'],
                $params['purchase_order_kitchen_tax'],
                $params['upduser'],
            ]
        );
    }

    public function updateData($params)
    {
        return DB::update(
            "UPDATE trpurchaseorderkitchenhd
            SET
            purchase_order_kitchen_date = ?,
            purchase_order_kitchen_supplier_id = ?,
            purchase_order_kitchen_supplier_name = ?,
            purchase_order_kitchen_pic_name = ?,
            purchase_order_kitchen_pic_phone = ?,
            purchase_order_kitchen_address = ?,
            purchase_order_kitchen_discount = ?,
            purchase_order_kitchen_tax = ?,
            upddate = getdate(),
            upduser = ?
            WHERE purchase_order_kitchen_id = ?",
            [
                $params['purchase_order_kitchen_date'],
                $params['purchase_order_kitchen_supplier_id'],
                $params['purchase_order_kitchen_supplier_name'],
                $params['purchase_order_kitchen_pic_name'],
                $params['purchase_order_kitchen_pic_phone'],
                $params['purchase_order_kitchen_address'],
                $params['purchase_order_kitchen_discount'],
                $params['purchase_order_kitchen_tax'],
                $params['upduser'],
                $params['purchase_order_kitchen_id'],
            ]
        );
    }

    public function getAllData($params)
    {
        return DB::select(
            "SELECT
            a.purchase_order_kitchen_id,
            a.purchase_order_kitchen_date,
            a.purchase_order_kitchen_supplier_id,
            a.purchase_order_kitchen_supplier_name,
            a.purchase_order_kitchen_pic_name,
            a.purchase_order_kitchen_pic_phone,
            a.purchase_order_kitchen_address,
            a.purchase_order_kitchen_discount,
            a.purchase_order_kitchen_tax,
            a.purchase_order_kitchen_tax_amount,
            a.purchase_order_kitchen_subtotal,
            a.purchase_order_kitchen_grandtotal,
            a.upddate,
            a.upduser
            FROM trpurchaseorderkitchenhd a
            WHERE convert(varchar(10), a.purchase_order_kitchen_date, 112) BETWEEN ? AND ?
            AND (
                ISNULL(a.purchase_order_kitchen_id, '') LIKE ?
                OR ISNULL(a.purchase_order_kitchen_supplier_name, '') LIKE ?
            )
            ORDER BY a.purchase_order_kitchen_id DESC",
            [
                $params['dari'],
                $params['sampai'],
                '%' . ($params['search_keyword'] ?? '') . '%',
                '%' . ($params['search_keyword'] ?? '') . '%',
            ]
        );
    }

    public function getDataById($id)
    {
        return DB::selectOne(
            "SELECT
            a.purchase_order_kitchen_id,
            a.purchase_order_kitchen_date,
            a.purchase_order_kitchen_supplier_id,
            a.purchase_order_kitchen_supplier_name,
            a.purchase_order_kitchen_pic_name,
            a.purchase_order_kitchen_pic_phone,
            a.purchase_order_kitchen_address,
            a.purchase_order_kitchen_discount,
            a.purchase_order_kitchen_tax,
            a.purchase_order_kitchen_tax_amount,
            a.purchase_order_kitchen_subtotal,
            a.purchase_order_kitchen_grandtotal,
            a.upddate,
            a.upduser
            FROM trpurchaseorderkitchenhd a
            WHERE a.purchase_order_kitchen_id = ?",
            [$id]
        );
    }

    public function hitungTotal($id)
    {
        return DB::selectOne(
            "SELECT
                k.purchase_order_kitchen_id,
                k.subtotal AS purchase_order_kitchen_subtotal,
                CASE
                    WHEN k.base_amount > 0 AND k.purchase_order_kitchen_tax > 0
                        THEN k.base_amount * k.purchase_order_kitchen_tax * 0.01
                    ELSE 0
                END AS purchase_order_kitchen_tax_amount,
                k.base_amount +
                (CASE
                    WHEN k.base_amount > 0 AND k.purchase_order_kitchen_tax > 0
                        THEN k.base_amount * k.purchase_order_kitchen_tax * 0.01
                    ELSE 0
                END) AS purchase_order_kitchen_grandtotal
            FROM (
                SELECT
                    b.purchase_order_kitchen_id,
                    ISNULL(SUM(a.purchase_order_kitchen_detail_total), 0) AS subtotal,
                    b.purchase_order_kitchen_discount,
                    b.purchase_order_kitchen_tax,
                    CASE
                        WHEN ISNULL(SUM(a.purchase_order_kitchen_detail_total), 0) - b.purchase_order_kitchen_discount < 0 THEN 0
                        ELSE ISNULL(SUM(a.purchase_order_kitchen_detail_total), 0) - b.purchase_order_kitchen_discount
                    END AS base_amount
                FROM trpurchaseorderkitchenhd b
                LEFT JOIN trpurchaseorderkitchendt a
                    ON a.purchase_order_kitchen_id = b.purchase_order_kitchen_id
                WHERE b.purchase_order_kitchen_id = ?
                GROUP BY
                    b.purchase_order_kitchen_id,
                    b.purchase_order_kitchen_discount,
                    b.purchase_order_kitchen_tax
            ) AS k",
            [$id]
        );
    }

    public function updateTotal($params)
    {
        return DB::update(
            "UPDATE trpurchaseorderkitchenhd
            SET
            purchase_order_kitchen_subtotal = ?,
            purchase_order_kitchen_tax_amount = ?,
            purchase_order_kitchen_grandtotal = ?
            WHERE purchase_order_kitchen_id = ?",
            [
                $params['purchase_order_kitchen_subtotal'],
                $params['purchase_order_kitchen_tax_amount'],
                $params['purchase_order_kitchen_grandtotal'],
                $params['purchase_order_kitchen_id'],
            ]
        );
    }

    public function cekData($id)
    {
        return DB::selectOne(
            "SELECT purchase_order_kitchen_id
            FROM trpurchaseorderkitchenhd
            WHERE purchase_order_kitchen_id = ?",
            [$id]
        );
    }

    public function generatePurchaseOrderKitchenId($purchaseOrderKitchenDate, $supplierName)
    {
        $timestamp = strtotime($purchaseOrderKitchenDate);

        if ($timestamp === false) {
            $timestamp = time();
        }

        $monthRoman = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $month = (int) date('n', $timestamp);
        $year = date('Y', $timestamp);
        $romanMonth = $monthRoman[$month];

        $supplierCode = $this->buildSupplierInitial($supplierName);

        $suffix = '/INV/' . $supplierCode . '/' . $romanMonth . '/' . $year;

        $lastData = DB::selectOne(
            "SELECT TOP 1 purchase_order_kitchen_id
            FROM trpurchaseorderkitchenhd
            WHERE purchase_order_kitchen_id LIKE ?
            ORDER BY purchase_order_kitchen_id DESC",
            ['%'.$suffix]
        );

        $lastNumber = 0;

        if (!empty($lastData) && !empty($lastData->purchase_order_kitchen_id)) {
            $lastNumber = (int) substr($lastData->purchase_order_kitchen_id, 0, 3);
        }

        $nextNumber = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);

        return $nextNumber . $suffix;
    }

    public function deleteData($id)
    {
        return DB::delete(
            "DELETE FROM trpurchaseorderkitchenhd WHERE purchase_order_kitchen_id = ?",
            [$id]
        );
    }

    private function buildSupplierInitial($supplierName)
    {
        $clean = preg_replace('/[^A-Za-z0-9\s]/', ' ', (string) $supplierName);
        $parts = preg_split('/\s+/', trim($clean));

        $initial = '';
        foreach ($parts as $part) {
            if ($part !== '') {
                $initial .= strtoupper(substr($part, 0, 1));
            }
        }

        return $initial !== '' ? $initial : 'SUP';
    }
}

?>
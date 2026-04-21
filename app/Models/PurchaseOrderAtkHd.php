<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

class PurchaseOrderAtkHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trpurchaseorderatkhd';

    public $timestamps = false;

    public function insertData($params)
    {
        return DB::insert(
            "INSERT INTO trpurchaseorderatkhd
            (purchase_order_atk_id, purchase_order_atk_date, purchase_order_atk_date_costing, purchase_order_atk_supplier_id,
            purchase_order_atk_supplier_name, purchase_order_atk_pic_name, purchase_order_atk_pic_phone,
            purchase_order_atk_to, purchase_order_atk_address, purchase_order_atk_note, purchase_order_atk_discount, purchase_order_atk_tax,
            purchase_order_atk_tax_amount, purchase_order_atk_koefisien, purchase_order_atk_budget,
            purchase_order_atk_budget_over, purchase_order_atk_subtotal, purchase_order_atk_grandtotal,
            upddate, upduser)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, getdate(), ?)",
            [
                $params['purchase_order_atk_id'],
                $params['purchase_order_atk_date'],
                $params['purchase_order_atk_date_costing'] ?? null,
                $params['purchase_order_atk_supplier_id'],
                $params['purchase_order_atk_supplier_name'],
                $params['purchase_order_atk_pic_name'],
                $params['purchase_order_atk_pic_phone'],
                $params['purchase_order_atk_to'],
                $params['purchase_order_atk_address'],
                $params['purchase_order_atk_note'],
                $params['purchase_order_atk_discount'],
                $params['purchase_order_atk_tax'],
                0, // purchase_order_atk_tax_amount
                $params['purchase_order_atk_koefisien'],
                $params['purchase_order_atk_budget'],
                $params['purchase_order_atk_budget_over'],
                0, // purchase_order_atk_subtotal
                0, // purchase_order_atk_grandtotal
                $params['upduser'],
            ]
        );
    }

    public function updateData($params)
    {
        return DB::update(
            "UPDATE trpurchaseorderatkhd
            SET
            purchase_order_atk_date = ?,
            purchase_order_atk_date_costing = ?,
            purchase_order_atk_supplier_id = ?,
            purchase_order_atk_supplier_name = ?,
            purchase_order_atk_pic_name = ?,
            purchase_order_atk_pic_phone = ?,
            purchase_order_atk_to = ?,
            purchase_order_atk_address = ?,
            purchase_order_atk_note = ?,
            purchase_order_atk_discount = ?,
            purchase_order_atk_tax = ?,
            purchase_order_atk_koefisien = ?,
            purchase_order_atk_budget = ?,
            purchase_order_atk_budget_over = ?,
            upddate = getdate(),
            upduser = ?
            WHERE purchase_order_atk_id = ?",
            [
                $params['purchase_order_atk_date'],
                $params['purchase_order_atk_date_costing'] ?? null,
                $params['purchase_order_atk_supplier_id'],
                $params['purchase_order_atk_supplier_name'],
                $params['purchase_order_atk_pic_name'],
                $params['purchase_order_atk_pic_phone'],
                $params['purchase_order_atk_to'],
                $params['purchase_order_atk_address'],
                $params['purchase_order_atk_note'],
                $params['purchase_order_atk_discount'],
                $params['purchase_order_atk_tax'],
                $params['purchase_order_atk_koefisien'],
                $params['purchase_order_atk_budget'],
                $params['purchase_order_atk_budget_over'],
                $params['upduser'],
                $params['purchase_order_atk_id'],
            ]
        );
    }

    public function getAllData($params)
    {
        return DB::select(
            "SELECT
            a.purchase_order_atk_id,
            a.purchase_order_atk_date,
            a.purchase_order_atk_date_costing,
            a.purchase_order_atk_supplier_id,
            ISNULL(s.nmsupplier, a.purchase_order_atk_supplier_name) AS purchase_order_atk_supplier_name,
            ISNULL(s.cp, a.purchase_order_atk_pic_name) AS purchase_order_atk_pic_name,
            ISNULL(s.hp, a.purchase_order_atk_pic_phone) AS purchase_order_atk_pic_phone,
            a.purchase_order_atk_to,
            a.purchase_order_atk_address,
            a.purchase_order_atk_note,
            a.purchase_order_atk_discount,
            a.purchase_order_atk_tax,
            CASE
                WHEN (dt.subtotal - a.purchase_order_atk_discount) > 0
                    AND a.purchase_order_atk_tax > 0
                    THEN (dt.subtotal - a.purchase_order_atk_discount) * a.purchase_order_atk_tax * 0.01
                ELSE 0
            END AS purchase_order_atk_tax_amount,
            a.purchase_order_atk_koefisien,
            a.purchase_order_atk_budget,
            a.purchase_order_atk_budget_over,
            dt.subtotal AS purchase_order_atk_subtotal,
            (CASE
                WHEN (dt.subtotal - a.purchase_order_atk_discount) < 0
                    THEN 0
                ELSE (dt.subtotal - a.purchase_order_atk_discount)
            END) +
            (CASE
                WHEN (dt.subtotal - a.purchase_order_atk_discount) > 0
                    AND a.purchase_order_atk_tax > 0
                    THEN (dt.subtotal - a.purchase_order_atk_discount) * a.purchase_order_atk_tax * 0.01
                ELSE 0
            END) AS purchase_order_atk_grandtotal,
            ISNULL(s.bank_branch, '') AS purchase_order_atk_bank_branch,
            ISNULL(s.bank_account, '') AS purchase_order_atk_bank_account,
            ISNULL(s.bank_holder, '') AS purchase_order_atk_bank_holder,
            a.upddate,
            a.upduser
            FROM trpurchaseorderatkhd a
            LEFT JOIN mssupplier s ON CAST(a.purchase_order_atk_supplier_id AS VARCHAR(50)) = s.kdsupplier
            OUTER APPLY (
                SELECT ISNULL(SUM(ISNULL(d.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(d.purchase_order_atk_detail_price, 0)), 0) AS subtotal
                FROM trpurchaseorderatkdt d
                WHERE d.purchase_order_atk_id = a.purchase_order_atk_id
            ) dt
            WHERE convert(varchar(10), a.purchase_order_atk_date, 112) BETWEEN ? AND ?
            AND (
                ISNULL(a.purchase_order_atk_id, '') LIKE ?
                OR COALESCE(s.nmsupplier, a.purchase_order_atk_supplier_name, '') LIKE ?
            )
            ORDER BY a.purchase_order_atk_id DESC",
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
            a.purchase_order_atk_id,
            a.purchase_order_atk_date,
            a.purchase_order_atk_date_costing,
            a.purchase_order_atk_supplier_id,
            ISNULL(s.nmsupplier, a.purchase_order_atk_supplier_name) AS purchase_order_atk_supplier_name,
            ISNULL(s.cp, a.purchase_order_atk_pic_name) AS purchase_order_atk_pic_name,
            ISNULL(s.hp, a.purchase_order_atk_pic_phone) AS purchase_order_atk_pic_phone,
            a.purchase_order_atk_to,
            a.purchase_order_atk_address,
            a.purchase_order_atk_note,
            a.purchase_order_atk_discount,
            a.purchase_order_atk_tax,
            CASE
                WHEN (dt.subtotal - a.purchase_order_atk_discount) > 0
                    AND a.purchase_order_atk_tax > 0
                    THEN (dt.subtotal - a.purchase_order_atk_discount) * a.purchase_order_atk_tax * 0.01
                ELSE 0
            END AS purchase_order_atk_tax_amount,
            a.purchase_order_atk_koefisien,
            a.purchase_order_atk_budget,
            a.purchase_order_atk_budget_over,
            dt.subtotal AS purchase_order_atk_subtotal,
            (CASE
                WHEN (dt.subtotal - a.purchase_order_atk_discount) < 0
                    THEN 0
                ELSE (dt.subtotal - a.purchase_order_atk_discount)
            END) +
            (CASE
                WHEN (dt.subtotal - a.purchase_order_atk_discount) > 0
                    AND a.purchase_order_atk_tax > 0
                    THEN (dt.subtotal - a.purchase_order_atk_discount) * a.purchase_order_atk_tax * 0.01
                ELSE 0
            END) AS purchase_order_atk_grandtotal,
            ISNULL(s.bank_branch, '') AS purchase_order_atk_bank_branch,
            ISNULL(s.bank_account, '') AS purchase_order_atk_bank_account,
            ISNULL(s.bank_holder, '') AS purchase_order_atk_bank_holder,
            a.upddate,
            a.upduser
            FROM trpurchaseorderatkhd a
            LEFT JOIN mssupplier s ON CAST(a.purchase_order_atk_supplier_id AS VARCHAR(50)) = s.kdsupplier
            OUTER APPLY (
                SELECT ISNULL(SUM(ISNULL(d.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(d.purchase_order_atk_detail_price, 0)), 0) AS subtotal
                FROM trpurchaseorderatkdt d
                WHERE d.purchase_order_atk_id = a.purchase_order_atk_id
            ) dt
            WHERE a.purchase_order_atk_id = ?",
            [$id]
        );
    }

    public function getSupplierDataById($supplierId)
    {
        return DB::selectOne(
            "SELECT
            kdsupplier AS supplier_id,
            nmsupplier AS supplier_name,
            ISNULL(cp, '') AS supplier_pic_name,
            ISNULL(hp, '') AS supplier_pic_phone
            FROM mssupplier
            WHERE kdsupplier = ?",
            [$supplierId]
        );
    }

    public function hitungTotal($id)
    {
        return DB::selectOne(
            "SELECT
                k.purchase_order_atk_id,
                k.subtotal AS purchase_order_atk_subtotal,
                CASE
                    WHEN k.base_amount > 0 AND k.purchase_order_atk_tax > 0
                        THEN k.base_amount * k.purchase_order_atk_tax * 0.01
                    ELSE 0
                END AS purchase_order_atk_tax_amount,
                k.base_amount +
                (CASE
                    WHEN k.base_amount > 0 AND k.purchase_order_atk_tax > 0
                        THEN k.base_amount * k.purchase_order_atk_tax * 0.01
                    ELSE 0
                END) AS purchase_order_atk_grandtotal
            FROM (
                SELECT
                    b.purchase_order_atk_id,
                    ISNULL(SUM(ISNULL(a.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(a.purchase_order_atk_detail_price, 0)), 0) AS subtotal,
                    b.purchase_order_atk_discount,
                    b.purchase_order_atk_tax,
                    CASE
                        WHEN ISNULL(SUM(ISNULL(a.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(a.purchase_order_atk_detail_price, 0)), 0) - b.purchase_order_atk_discount < 0 THEN 0
                        ELSE ISNULL(SUM(ISNULL(a.purchase_order_atk_detail_qty_invoice, 0) * ISNULL(a.purchase_order_atk_detail_price, 0)), 0) - b.purchase_order_atk_discount
                    END AS base_amount
                FROM trpurchaseorderatkhd b
                LEFT JOIN trpurchaseorderatkdt a
                    ON a.purchase_order_atk_id = b.purchase_order_atk_id
                WHERE b.purchase_order_atk_id = ?
                GROUP BY
                    b.purchase_order_atk_id,
                    b.purchase_order_atk_discount,
                    b.purchase_order_atk_tax
            ) AS k",
            [$id]
        );
    }

    public function updateTotal($params)
    {
        return DB::update(
            "UPDATE trpurchaseorderatkhd
            SET
            purchase_order_atk_subtotal = ?,
            purchase_order_atk_tax_amount = ?,
            purchase_order_atk_grandtotal = ?,
            purchase_order_atk_budget_over = CASE
                WHEN ? - purchase_order_atk_budget > 0 THEN ? - purchase_order_atk_budget
                ELSE 0
            END
            WHERE purchase_order_atk_id = ?",
            [
                $params['purchase_order_atk_subtotal'],
                $params['purchase_order_atk_tax_amount'],
                $params['purchase_order_atk_grandtotal'],
                $params['purchase_order_atk_grandtotal'],
                $params['purchase_order_atk_grandtotal'],
                $params['purchase_order_atk_id'],
            ]
        );
    }

    public function cekData($id)
    {
        return DB::selectOne(
            "SELECT purchase_order_atk_id
            FROM trpurchaseorderatkhd
            WHERE purchase_order_atk_id = ?",
            [$id]
        );
    }

    public function generatePurchaseOrderAtkId($purchaseOrderAtkDate, $supplierName)
    {
        $timestamp = strtotime($purchaseOrderAtkDate);

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
            "SELECT TOP 1 purchase_order_atk_id
            FROM trpurchaseorderatkhd
            WHERE purchase_order_atk_id LIKE ?
            ORDER BY purchase_order_atk_id DESC",
            ['%'.$suffix]
        );

        $lastNumber = 0;

        if (!empty($lastData) && !empty($lastData->purchase_order_atk_id)) {
            $lastNumber = (int) substr($lastData->purchase_order_atk_id, 0, 3);
        }

        $nextNumber = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);

        return $nextNumber . $suffix;
    }

    public function deleteData($id)
    {
        return DB::delete(
            "DELETE FROM trpurchaseorderatkhd WHERE purchase_order_atk_id = ?",
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class TrBreakDownMenuDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trbreakdownmenudt';
    public $timestamps = false;

    public function getAllData($hdCode)
    {
        return DB::select(
            "SELECT
                d.trbreakdownmenudt_hd_code,
                d.trbreakdownmenudt_itemid,
                m.NmMenu as trbreakdownmenudt_itemname,
                d.trbreakdownmenudt_qty,
                d.trbreakdownmenudt_uomid,
                d.trbreakdownmenudt_note
            FROM trbreakdownmenudt d
            LEFT JOIN MsMenuHd m ON d.trbreakdownmenudt_itemid = m.KdMenu
            WHERE d.trbreakdownmenudt_hd_code = :hd_code
            ORDER BY d.trbreakdownmenudt_itemid",
            [
                'hd_code' => $hdCode,
            ]
        );
    }

    public function insertData($params)
    {
        // Validasi: itemid harus ada di MsMenuHd
        $itemid = $params['trbreakdownmenudt_itemid'];
        $cek = DB::selectOne("SELECT KdMenu FROM MsMenuHd WHERE KdMenu = :itemid", [
            'itemid' => $itemid
        ]);
        if (!$cek) {
            throw new \Exception('Kode Barang tidak valid atau tidak termasuk dalam master');
        }
        return DB::insert(
            "INSERT INTO trbreakdownmenudt
            (trbreakdownmenudt_hd_code, trbreakdownmenudt_itemid, trbreakdownmenudt_qty, trbreakdownmenudt_uomid, trbreakdownmenudt_note)
            VALUES
            (:trbreakdownmenudt_hd_code, :trbreakdownmenudt_itemid, :trbreakdownmenudt_qty, :trbreakdownmenudt_uomid, :trbreakdownmenudt_note)",
            [
                'trbreakdownmenudt_hd_code' => $params['trbreakdownmenudt_hd_code'],
                'trbreakdownmenudt_itemid' => $params['trbreakdownmenudt_itemid'],
                'trbreakdownmenudt_qty' => $params['trbreakdownmenudt_qty'],
                'trbreakdownmenudt_uomid' => $params['trbreakdownmenudt_uomid'],
                'trbreakdownmenudt_note' => $params['trbreakdownmenudt_note'],
            ]
        );
    }

    public function deleteByHdCode($hdCode)
    {
        return DB::delete(
            "DELETE FROM trbreakdownmenudt WHERE trbreakdownmenudt_hd_code = :hd_code",
            [
                'hd_code' => $hdCode,
            ]
        );
    }
}

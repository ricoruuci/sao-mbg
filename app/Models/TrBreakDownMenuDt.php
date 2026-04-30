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

    public function getDataBahanBaku($id)
    {
        return DB::select(
            "SELECT 
                l.kdgroupbb,
                m.nmgroupbb,
                k.kdbb,
                l.nmbb, 
                isnull(sum(k.trbreakdownmenudt_qty*k.trbreakdownhd_qty_beneficiaries*k.jumlah),0) as total,
                k.kdsat
            FROM (
                select 
                    a.trbreakdownmenudt_itemid,
                    a.trbreakdownmenudt_qty,
                    b.trbreakdownhd_qty_beneficiaries,
                    c.kdbb,
                    c.jumlah,
                    c.kdsat 
                FROM trbreakdownmenudt a
                inner join trbreakdownmenuhd b on a.trbreakdownmenudt_hd_code=b.trbreakdownhd_code
                inner join msmenudt c on a.trbreakdownmenudt_itemid=c.kdmenu
                where trbreakdownmenudt_hd_code=:id
            ) as k
            inner join msbahanbaku l on k.kdbb=l.kdbb
            inner join msgroupbb m on l.kdgroupbb=m.kdgroupbb
            GROUP BY k.kdbb,k.kdsat,l.nmbb,l.kdgroupbb,m.nmgroupbb
            ORDER BY m.nmgroupbb,l.nmbb",
            [
                'id' => $id,
            ]
        );
    }
}

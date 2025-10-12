<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class JualDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trjualdt';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO trjualdt
            (nota, nodetil, kdmenu, jumlah, harga, disc, discfrom, upddate, upduser, fgprint)
            VALUES
            (:nota, :urut, :kdmenu, :qty, :price, 0, 'H', getdate(), :upduser, 0)",
            [
                'nota' => $params['nota_jual'],
                'urut' => $params['urut'],
                'kdmenu' => $params['menu_id'],
                'qty' => $params['qty'],
                'price' => $params['price'],
                'upduser' => $params['upduser']
            ]
        );


        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trjualdt where nota = :nota",
            [
                'nota' => $id
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::select(
            "SELECT
            a.nota as nota_jual,
            a.kdmenu as menu_id,
            b.nmmenu as menu_name,
            a.jumlah as qty,
            a.harga as price,
            a.jumlah*a.harga as total,
            isnull(a.keterangan,'') as note,
            a.upddate,
            a.upduser
            from trjualdt a
            left join msmenuhd b on a.kdmenu=b.kdmenu
            where a.nota = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function deleteAllItem($id) : void
    {
        $result = DB::delete(
            "DELETE FROM allitem where voucherno= :id",
            [
                'id' => $id
            ]
        );

    }

    function insertAllItem($id,$company_id) : void
    {
        // Step 1: Ambil kebutuhan bahan baku dari penjualan
        $bahanList = DB::select("
            SELECT 
                a.nota,
                a.nodetil,
                b.tgljual,
                c.kdbb,
                a.kdmenu,
                a.jumlah * c.jumlah AS total_qty_bb,
                b.kdpos,
                a.upduser,
                a.harga,
                e.company_code,
                b.company_id
            FROM trjualdt a
            JOIN trjualhd b ON a.nota = b.nota
            LEFT JOIN msmenudt c ON a.kdmenu = c.kdmenu
            left join mscabang e on b.company_id=e.company_id
            WHERE a.nota = :nota", 
            [
                'nota' => $id
            ]
        );

        foreach ($bahanList as $bahan) {
            $neededQty = $bahan->total_qty_bb;
            $remainingQty = $neededQty;
            $tgljual = $bahan->tgljual;
            $upduser = $bahan->upduser;
            $harga = $bahan->harga;
            $company_ids = $bahan->company_id;
            $warehouse = $bahan->company_code;
            // Step 2: Ambil FIFO stock dari allitem (sisa qty)
            $stockList = DB::select("
                SELECT 
                    a.transdate,
                    a.voucherno,
                    a.itemid,
                    a.qty,
                    a.price,
                    a.fgtrans,
                    a.warehouseid,
                    a.actorid,
                    a.reffid,
                    a.hpp,
                    a.upddate,
                    a.upduser,
                    (a.qty - ISNULL((
                        SELECT SUM(x.qty)
                        FROM allitem x
                        WHERE x.reffid = a.reffid 
                        AND x.hpp = a.hpp 
                        AND x.itemid = a.itemid 
                        AND x.warehouseid = a.warehouseid 
                        AND x.company_id = a.company_id
                        AND x.fgtrans > 50
                    ), 0)) AS sisa_qty
                FROM allitem a
                WHERE a.fgtrans < 50 AND a.itemid = :itemid
                ORDER BY a.transdate, a.voucherno ",  
                [
                    'itemid' => $bahan->kdbb
                ]);

            foreach ($stockList as $stock) {
                if ($remainingQty <= 0) break;

                $sisa = (float) $stock->sisa_qty;
                if ($sisa <= 0) continue;

                $usedQty = min($sisa, $remainingQty);
                $remainingQty -= $usedQty;

                $result = DB::insert(
                    "INSERT into allitem (transdate,voucherno,itemid,qty,price,fgtrans,warehouseid,actorid,reffid,hpp,upddate,upduser,company_id)
                    SELECT :transdate, :voucherno, :itemid, :qty, :price, :fgtrans, :warehouseid, :actorid, :reffid, :hpp, getdate(), :upduser, :company_id",
                    [
                        'transdate'    => $tgljual,
                        'voucherno'    => $id,
                        'itemid'       => $stock->itemid,
                        'qty'          => $usedQty,
                        'price'        => $harga,
                        'fgtrans'      => 51,
                        'warehouseid'  => $warehouse,
                        'actorid'      => $id,
                        'reffid'       => $stock->reffid,
                        'hpp'          => $stock->hpp,
                        'upduser'      => $upduser,
                        'company_id'   => $company_ids 
                    ]
                );
            }
        }

    }

    function updateAllTransaction($params) : void
    {
        $result = DB::delete(
            "DELETE FROM AllTransaction where VoucherNo= :id",
            [
                'id' => $params['id']
            ]
        );

        $result = DB::insert(
            "INSERT into AllTransaction (transdate,voucherno,fgtrans,company_id)
            SELECT :transdate, :id, :fgtrans, :company_id ",
            [
                'transdate' => $params['transdate'],
                'id' => $params['id'],
                'fgtrans' => $params['fgtrans'],
                'company_id' => $params['company_id'],
            ]
        );

    }


}

?>

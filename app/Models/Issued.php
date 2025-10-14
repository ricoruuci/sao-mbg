<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Issued extends BaseModel
{
    use HasFactory;

    protected $table = 'trissued';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT trissued (transdate, kdbb, satuan, qty, upddate, upduser, company_id)
            VALUES (:transdate, :kdbb, :satuan, :qty, getdate(), :upduser, :company_id)",
            [
                'transdate' => $params['tanggal'],
                'kdbb' => $params['bahan_baku_id'],
                'satuan' => $params['satuan'],
                'qty' => $params['jumlah'],
                'upduser' => $params['upduser'],
                'company_id' => $params['company_id']
            ]
        );

        return $result;
    }

    function deleteData($params)
    {
        $result = DB::insert(
            "DELETE FROM trissued WHERE convert(varchar(10),transdate,112) = :transdate AND company_id = :company_id;",
            [
                'transdate' => $params['tanggal'],
                'company_id' => $params['company_id']
            ]
        );

        return $result;
    }

    function getAllData($params)    
    {

        $result = DB::select(
            "SELECT a.id,a.transdate as tanggal,a.kdbb as bahan_baku_id,b.nmbb as bahan_baku_name,a.satuan,a.qty as jumlah,a.upddate,a.upduser
            from trissued a
            left join msbahanbaku b on a.kdbb=b.kdbb
            where a.company_id = :id
            and convert(varchar,a.transdate,112) = :transdate ",
            [
                'id' => $params['company_id'],
                'transdate' => $params['tanggal']
            ]
        );

        return $result;
    }

    function deleteAllItem($params) : void
    {
        $result = DB::delete(
            "DELETE FROM AllItem where convert(varchar(10),transdate,112)= :tanggal and company_id=:company_id and fgtrans=99",
            [
                'tanggal' => $params['tanggal'],
                'company_id' => $params['company_id']
            ]
        );

    }

    function insertAllItem($params) : void
    {
        // Step 1: Ambil kebutuhan bahan baku dari penjualan
        $bahanList = DB::select("SELECT 
                a.transdate as tgljual,
                a.kdbb,
                a.satuan,
                case when a.satuan=c.satkecil then a.qty else a.qty * c.jumsat end AS total_qty_bb,
                a.upduser,
                a.upddate,
                a.company_id,
                e.company_code,
                0 as harga
            FROM trissued a
            LEFT JOIN msbahanbaku c ON a.kdbb = c.kdbb
            left join mscabang e on a.company_id=e.company_id
            WHERE convert(varchar,a.transdate,112) = :tanggal
            AND a.company_id = :company_id", 
            [
                'tanggal' => $params['tanggal'],
                'company_id' => $params['company_id']
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
                        'voucherno'    => 'dailyissued',
                        'itemid'       => $stock->itemid,
                        'qty'          => $usedQty,
                        'price'        => $harga,
                        'fgtrans'      => 99,
                        'warehouseid'  => $warehouse,
                        'actorid'      => 'dailyissued',
                        'reffid'       => $stock->reffid,
                        'hpp'          => $stock->hpp,
                        'upduser'      => $upduser,
                        'company_id'   => $company_ids 
                    ]
                );
            }
        }

    }

}

?>

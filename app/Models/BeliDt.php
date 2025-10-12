<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class BeliDt extends BaseModel
{
    use HasFactory;

    protected $table = 'trbelibbdt';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO trbelibbdt
            (nota, kdsupplier, kdbb, jml, harga, disc, upddate, upduser, kdsat)
            VALUES
            (:nota, :kdsupplier, :kdbb, :jml, :harga, 0, getdate(), :upduser, :kdsat)",
            [
                'nota' => $params['nota_beli'],
                'kdsupplier' => $params['supplier_id'],
                'kdbb' => $params['bahan_baku_id'],
                'jml' => $params['qty'],
                'harga' => $params['price'],
                'upduser' => $params['upduser'],
                'kdsat' => $params['satuan']
            ]
        );


        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trbelibbdt where nota = :nota ",
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
            a.nota as nota_beli,
            a.kdbb as bahan_baku_id,
            b.nmbb as bahan_baku_name,
            a.jml as qty,
            a.harga as price,
            a.jml*a.harga as total,
            a.kdsat as satuan,
            a.upddate,
            a.upduser
            from trbelibbdt a
            left join msbahanbaku b on a.kdbb=b.kdbb
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
            "DELETE FROM AllItem where VoucherNo= :id",
            [
                'id' => $id
            ]
        );

    }

    function insertAllItem($id,$company_id) : void
    {
        $result = DB::insert(
            "INSERT into allitem (transdate,voucherno,itemid,qty,price,fgtrans,warehouseid,actorid,reffid,hpp,upddate,upduser,company_id)
            SELECT b.tglbeli,a.nota,a.kdbb,a.jml,a.harga,1,e.company_code,a.kdsupplier,a.nota,a.harga,getdate(),a.upduser,b.company_id
            from trbelibbdt a
            inner join trbelibbhd b on a.nota=b.nota and a.kdsupplier=b.kdsupplier
            left join msbahanbaku d on a.kdbb=d.kdbb
            left join mscabang e on b.company_id=e.company_id
            where a.nota=:id  and b.company_id=:company_id ",
            [
                'id' => $id,
                'company_id' => $company_id
            ]
        );

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
            SELECT :transdate, :id, :fgtrans,:company_id ",
            [
                'transdate' => $params['transdate'],
                'id' => $params['id'],
                'fgtrans' => $params['fgtrans'],
                'company_id' => $params['company_id']
            ]
        );

    }

}

?>

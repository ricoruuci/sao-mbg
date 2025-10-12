<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class CFTrKKBBDt extends Model //nama class
{
    use HasFactory;

    protected $table = 'cftrkkbbdt'; //sesuaiin nama tabel

    public $timestamps = false;

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO cftrkkbbdt
            (voucherid,rekeningid,note,amount,upddate,upduser,jenis) 
            VALUES 
            (:voucherid,:rekeningid,:note,:amount,getDate(),:upduser,:jenis)", 
            [
                'voucherid' => $param['voucherid'],
                'rekeningid' => $param['rekeningid'],
                'note' => $param['note'],
                'amount' => $param['amount'],
                'upduser' => $param['upduser'],
                'jenis' => $param['jenis']
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::select(
            "SELECT case when b.FlagKKBB in ('ark','arc','arb','apk','apb','apc') then 'T' else 'Y' end as userinput,
            a.voucherid as voucher_id,a.rekeningid,c.rekeningname,a.note,a.amount,a.upddate,a.upduser,a.jenis from cftrkkbbdt a
            inner join CFTrKKBBHd b on a.voucherid=b.voucherid
            left join cfmsrekening c on a.RekeningID=c.rekeningid WHERE a.voucherid = :voucherid ",
            [
                'voucherid' => $param['voucher_id']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM cftrkkbbdt WHERE voucherid = :voucherid',
            [
                'voucherid' => $param['voucherid']
            ]
        );

        return $result;
    }

    function cariNota($param)
    {
        
        $result = DB::select(
            "SELECT *,k.total-k.bayar as sisa from (
            select a.nota as nota,a.tglbeli as transdate,a.ttlpb as total,
            isnull((select sum(case when x.jenis='d' then x.amount else x.amount*-1 end) from cftrkkbbdt x 
            inner join cftrkkbbhd y on x.voucherid=y.voucherid where x.note=a.nota and convert(varchar(10),y.transdate,112) <= :tgl1),0) as bayar
            from trbelibbhd a 
            where convert(varchar(10),a.tglbeli,112) <= :tgl2 and a.kdsupplier=:actor and a.company_id=:company_id
            ) as k
            where k.total-k.bayar<>0
            order by k.nota ",
            [
                'actor' => $param['actor'],
                'tgl1' => $param['transdate'],
                'tgl2' => $param['transdate'],
                'company_id' => $param['company_id']
            ]
        );
        

        return $result;
    }
}

?>
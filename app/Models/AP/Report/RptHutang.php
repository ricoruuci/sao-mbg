<?php

namespace App\Models\AP\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class RptHutang extends Model
{
    function laporanHutang($param)
    {
        if($param['fglunas']=="Y"){
        $addcond = "and isnull(k.total-k.retur-k.bayar,0) > 0";
        }
        else
        {
        $addcond = "";
        }

        $result = DB::select(
        "SELECT k.suppid,l.suppname,convert(varchar(10),k.transdate,111) as tanggal,k.purchaseid,k.konsinyasiid as grn,k.poid,
        isnull(k.total,0) as total,isnull(k.retur,0) as retur,isnull(k.bayar,0) as bayar,isnull(k.total-k.retur-k.bayar,0) as sisa
        
        from (
 
        select a.suppid,a.transdate,a.purchaseid,a.konsinyasiid,b.poid,a.currid,isnull(a.ttlpb,0) as total,
        isnull((select isnull(sum(price*qty),0) from aptrreturndt f 
        inner join aptrreturnhd g  on f.returnid=g.returnid where g.flagretur='b' and f.purchaseid=a.purchaseid and g.suppid=a.suppid and 
        convert(varchar(8),g.transdate,112) <= :tgl1 ),0) as retur,
        
        isnull((select isnull(sum(l.amount),0) from cftrkkbbdt l inner join cftrkkbbhd h on l.voucherid=h.voucherid 
        where l.note=a.purchaseid and convert(varchar(8),h.transdate,112) <= :tgl2
        and l.rekeningid=(select drpb from samsset)),0) as bayar 
        
        from aptrpurchasehd a
        inner join aptrkonsinyasihd b on a.konsinyasiid=b.konsinyasiid
        ) as k 
        inner join apmssupplier l on k.suppid=l.suppid

        where convert(varchar(8),k.transdate,112) <= :tgl3 ".$addcond." order by convert(varchar(8),k.transdate,112) ",
            [
                'tgl1' => $param['tanggal'],
                'tgl2' => $param['tanggal'],
                'tgl3' => $param['tanggal']
            ]
        );

        return $result;

    }



}

?>
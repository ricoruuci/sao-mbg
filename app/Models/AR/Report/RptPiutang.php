<?php

namespace App\Models\AR\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class RptPiutang extends Model
{
    function laporanPiutang($param)
    {
        if($param['fglunas']=="Y"){
        $addcond = "and isnull(k.ttlpj-k.kredit-k.debit,0)<>0";
        }
        else
        {
        $addcond = "";
        }

        $result = DB::select(
        "SELECT k.custid, l.custname, convert(varchar(10),k.transdate,111) as tanggal, case when k.nama='' then k.saleid else k.saleid+' ('+k.nama+')' end as invoice,
        isnull(k.ttlpj,0) as total, isnull(k.kredit,0) as retur, isnull(k.debit,0) as bayar, isnull(k.ttlpj-k.kredit-k.debit,0) as sisa 

        from (

        select a.custid,a.currid,a.transdate,a.saleid,a.nama,isnull(a.ttlpj,0) as ttlpj,
        isnull((select isnull(sum(price*qty),0) from artrreturpenjualandt f inner join artrreturpenjualanhd g 
        on f.returnid=g.returnid where g.flagretur='b' and f.saleid=a.saleid 
        and g.custid=a.custid and convert(varchar(8),g.transdate,112) <=:tgl1 ),0) as kredit,
        (select isnull(sum(l.amount),0) from cftrkkbbdt l inner join cftrkkbbhd q on l.voucherid=q.voucherid 
        where l.note = a.saleid and convert(varchar(8),q.transdate,112) <=:tgl2
        and l.rekeningid=(select drpj from samsset)) as debit 
        from artrpenjualanhd a
        ) as k 
        inner join armscustomer l on k.custid=l.custid 
        where convert(varchar(8),k.transdate,112) <=:tgl3 ".$addcond." order by convert(varchar(8),k.transdate,112)",
            [
                'tgl1' => $param['tanggal'],
                'tgl2' => $param['tanggal'],
                'tgl3' => $param['tanggal']
            ]
        );

        return $result;

    }

    function laporanRekapPenjualan($param)
    {

        if($param['fglunas']==1)
        {
            $addcond = " AND L.TotalInv-L.Bayar <= 0 ";
        }
        else if($param['fglunas']==2)
        {
            $addcond = " AND L.TotalInv-L.Bayar <= 0 AND L.Lama <= 30 ";
        }
        else
        {
            $addcond = "";
        }

        $result = DB::select(
        "SELECT l.*,m.salesname,case when l.gp > isnull(m.tomzet,0) then l.margin*m.kom1*0.01 else l.margin*m.kom2*0.01 end as hkomisi from (

        select k.*,isnull(jual-modal-komisi,0) as margin,isnull(ppnout-ppnin,0) as ppnmargin,
        
        (select isnull(sum(x.qty*(x.price-x.modal-x.komisi)),0) from artrpenjualandt x inner join artrpenjualanhd y on x.saleid=y.saleid 
        where y.salesid=k.salesid and convert(varchar(8),y.transdate,112) between :dari1 and :sampai1 ) as gp,
        
        isnull((select sum(a.amount) from cftrkkbbdt a inner join cftrkkbbhd b on a.voucherid=b.voucherid and b.flagkkbb in ('ark','arb','arc') 
        where a.rekeningid=(select drpj from samsset) and a.note=k.saleid),0) as bayar,
        
        (select top 1 datediff(day,k.jatuhtempo,b.transdate) from cftrkkbbdt a inner join cftrkkbbhd b on a.voucherid=b.voucherid and b.flagkkbb in ('ark','arb','arc') 
        where a.rekeningid=(select drpj from samsset) and a.note=k.saleid order by convert(varchar(8),b.transdate,112) desc) as lama from (
        
        select isnull(b.poid,'') as poid,a.saleid,b.salesid,convert(varchar(10),b.transdate,103) as tgl,c.custname,d.itemname,b.fgtax,b.transdate,isnull(a.qty,0) as jumlah,
        isnull(a.price,0) as price,isnull(round(a.qty*a.price,2),0) as jual,dateadd(day,b.jatuhtempo,b.transdate) as jatuhtempo,
        isnull(round((case when b.fgtax='y' then (a.qty*a.price)*0.1 else 0 end),2),0) as ppnout,isnull(round(a.qty*a.modal,2),0) as modal,
        isnull(round((a.qty*a.modal/1.1*0.1),2),0) as ppnin,isnull(a.komisi,0) as komisi,isnull(b.ttlpj,0) as totalinv 
        
        from artrpenjualandt a 
        inner join artrpenjualanhd b on a.saleid=b.saleid 
        inner join armscustomer c on b.custid=c.custid 
        inner join inmsitem d on a.itemid=d.itemid
        ) as k
        ) as l 
        inner join armssales m on l.salesid=m.salesid 
        where convert(varchar(8),l.transdate,112) between :dari2 and :sampai2 ".$addcond." order by convert(varchar(8),l.transdate,112),l.saleid,l.salesid ",
            [
                'dari1' => $param['dari'],
                'dari2' => $param['dari'],
                'sampai1' => $param['sampai'],
                'sampai2' => $param['sampai']
            ]
        );

        return $result;

    }



}

?>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use App\Models\User;
use function PHPUnit\Framework\isNull;

class RptFinance extends BaseModel
{
    use HasFactory;

    function getRptBukuBesar($param)
    {
        if ($param['rekening_id']<>'') {
            $addCon = "and K.rekeningid='" . $param['rekening_id'] . "'";
        } else {
            $addCon = '';
        }

        if (!empty($params['company_id'])) 
        {
            $addquery = $this->queryAccounting([
                'company_id' => $params['company_id']
            ]);
        }
        else
        {
            $addquery = $this->queryAccounting([]);
        }

        $result = DB::select(
            "SELECT m.fgtipe as tipe,k.rekeningid,l.grouprekid,l.rekeningname,
            isnull(sum(case when convert(varchar(8),k.transdate,112) < :dari1 then (case when k.currid='idr' then k.amount else k.amount*k.rate end)*(case when k.jenis='d' then 1 else -1 end) else 0 end),0) as awal,
            isnull(sum(case when convert(varchar(8),k.transdate,112) between :dari2 and :sampai1 then (case when k.currid='idr' then k.amount else k.amount*k.rate end)*(case when k.jenis='d' then 1 else 0 end) else 0 end),0) as debet,
            isnull(sum(case when convert(varchar(8),k.transdate,112) between :dari3 and :sampai2 then (case when k.currid='idr' then k.amount else k.amount*k.rate end)*(case when k.jenis='k' then 1 else 0 end) else 0 end),0) as kredit,
            isnull(sum(case when convert(varchar(8),k.transdate,112) <= :sampai3 then (case when k.currid='idr' then k.amount else k.amount*k.rate end)*(case when k.jenis='d' then 1 else -1 end) else 0 end),0) as akhir
            from (
            $addquery
            ) as k
            inner join cfmsrekening l on k.rekeningid=l.rekeningid
            inner join cfmsgrouprek m on l.grouprekid=m.grouprekid
            where convert(varchar(8),k.transdate,112) <= :sampai and k.amount<> 0 $addCon
            group by m.fgtipe,k.rekeningid,l.rekeningname,l.grouprekid
            order by m.fgtipe,l.grouprekid,k.rekeningid",
            [
                'dari1' => $param['dari'],
                'dari2' => $param['dari'],
                'dari3' => $param['dari'],
                'sampai1' => $param['sampai'],
                'sampai2' => $param['sampai'],
                'sampai3' => $param['sampai'],
                'sampai' => $param['sampai']
            ]
        );

        foreach ($result as $row) {
            $endBalance = $row->awal;

            $cekdata = DB::select(
                "SELECT k.transdate,k.voucherid,isnull(k.note,'') as keterangan,k.amount as amount,UPPER(k.jenis) as jenis,k.jenis as flagkkbb
                from (
                $addquery
                ) as k
                where convert(varchar(8),k.transdate,112) between :dari and :sampai and k.rekeningid=:rekeningid 
                order by k.transdate,k.jenis,k.voucherid",
                [
                    'dari' => $param['dari'],
                    'sampai' => $param['sampai'],
                    'rekeningid' => $row->rekeningid,
                ]
            );

            foreach ($cekdata as $hasil) {
                if (strtoupper($hasil->jenis) == 'D') {
                    $endBalance = $endBalance + $hasil->amount;
                } else {
                    $endBalance = $endBalance - $hasil->amount;
                }

                $hasil->saldo = strval($endBalance);
            }


            $row->mutasi = $cekdata;
        }

        return $result;
    }

    function getRptLabaRugi($param)
    {

        if (!empty($params['company_id'])) 
        {
            $addquery = $this->queryAccounting([
                'company_id' => $params['company_id']
            ]);
        }
        else
        {
            $addquery = $this->queryAccounting([]);
        }

        $result = DB::select(
            "SELECT isnull(sum(k.jumlah),0) as laba from (
            select a.rekeningid,a.rekeningname,b.fgtipe as kode,
            isnull((select sum(case when x.jenis='k' then x.amount else x.amount*-1 end) from (
            $addquery
            ) as x
            where convert(varchar(8),x.transdate,112) between :dari and :sampai
            and x.rekeningid=a.rekeningid),0) as jumlah
            from cfmsrekening a
            inner join cfmsgrouprek b on a.grouprekid=b.grouprekid
            ) as k
            where k.kode in (4,5,6,7,8) and k.jumlah<>0 ",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai']
            ]
        );

        foreach ($result as $row) {

            $cekdata = DB::select(
                "SELECT k.kode,case when k.kode in (4) then 'PENDAPATAN' else 'PEMBELIAN & PENGELUARAN' end as keterangan,isnull(sum(case when k.kode=4 then k.jumlah else k.jumlah*-1 end),0) as total
                from (
                select a.rekeningid,a.rekeningname,b.fgtipe as kode,
                isnull((select sum(case when x.jenis='k' then x.amount else x.amount*-1 end) from (
                $addquery
                ) as x
                where convert(varchar(8),x.transdate,112) between :dari and :sampai
                and x.rekeningid=a.rekeningid),0) as jumlah
                from cfmsrekening a
                inner join cfmsgrouprek b on a.grouprekid=b.grouprekid
                ) as k
                where k.kode in (4,5,6,7,8) and k.jumlah<>0 group by k.kode order by k.kode",
                [
                    'dari' => $param['dari'],
                    'sampai' => $param['sampai']
                ]
            );

            foreach ($cekdata as $baris) {

                $cekdetail = DB::select(
                    "SELECT k.kode,k.rekeningid as grouprekid,k.rekeningname as grouprekname,isnull(sum(case when k.kode=4 then k.jumlah else k.jumlah*-1 end),0) as jumlah
                    from (
                    select b.fgtipe as kode,a.rekeningid,a.rekeningname,isnull((select sum(case when x.jenis='k' then x.amount else x.amount*-1 end) from (
                    $addquery
                    ) as x
                    where convert(varchar(8),x.transdate,112) between :dari and :sampai
                    and x.rekeningid=a.rekeningid),0) as jumlah
                    from cfmsrekening a
                    inner join cfmsgrouprek b on a.grouprekid=b.grouprekid
                    ) as k
                    where k.kode=:kode and k.kode in (4,5) and k.jumlah<>0
                    group by k.kode,k.rekeningid,k.rekeningname
                    order by k.kode,k.rekeningid",
                    [
                        'dari' => $param['dari'],
                        'sampai' => $param['sampai'],
                        'kode' => $baris->kode
                    ]
                );

                $baris->detail = $cekdetail;
            }

            $row->komponen = $cekdata;
        }

        return $result;
    }

    function getRptNeraca($param)
    {

        if (!empty($params['company_id'])) 
        {
            $addquery = $this->queryAccounting([
                'company_id' => $params['company_id']
            ]);
        }
        else
        {
            $addquery = $this->queryAccounting([]);
        }

        $result = DB::select(
            "SELECT k.fgtipe,k.subkomponen as keterangan,l.total as total from 
            (
                select 'A' as fgtipe,'TOTAL AKTIVA' as subkomponen union all 
                select 'P','TOTAL PASSIVA' 
            ) as K 
            inner join 
            (
            select p.fgtipe,case when p.fgtipe='a' then sum(p.jumlah) else sum(p.jumlah*-1) end as total from (
            select y.grouprekid,case when z.fgtipe in (4,5,6,7,8) then 3 else z.fgtipe end as tipe,case when z.fgtipe in (1,9) then 'A' else 'P' end as fgtipe,
            isnull((select sum(case when x.jenis='d' then x.amount else x.amount*-1 end) from (
            $addquery
            ) as x 
            where x.rekeningid=y.rekeningid and convert(varchar(8),x.transdate,112) <= :periode),0) as jumlah 
            from cfmsrekening y
            left join cfmsgrouprek z on y.grouprekid=z.grouprekid
            ) as P group by p.fgtipe 
            ) as L on k.fgtipe=l.fgtipe 
            order by k.fgtipe",
            [
                'periode' => $param['periode']
            ]
        );

        
        foreach ($result as $row) {

            $cekdata = DB::select(
                "SELECT K.fgtipe,K.SubKomponen as komponen,K.Tipe as tipe,L.Total as total FROM 
                (
                SELECT 'A' as FgTipe,'CURRENT ASSET' as SubKomponen,1 as Tipe UNION ALL 
                SELECT 'A','FIXED ASSET',9 UNION ALL 
                SELECT 'P','LIABILITIES',2 UNION ALL 
                SELECT 'P','CAPITAL',3
                ) as K 
                INNER JOIN 
                (
                SELECT P.Tipe,P.FgTipe,CASE WHEN P.FgTipe='A' THEN SUM(P.Jumlah) ELSE SUM(P.Jumlah*-1) END as Total FROM (
                SELECT Y.GroupRekID,CASE WHEN z.fgTipe IN (4,5,6,7,8) THEN 3 ELSE z.fgTipe END as Tipe,case when z.fgtipe in (1,9) then 'A' else 'P' end as FgTipe,
                ISNULL((SELECT SUM(CASE WHEN X.Jenis='D' THEN X.Amount ELSE X.Amount*-1 END) FROM (
                $addquery
                ) as X 
                WHERE X.RekeningID=Y.RekeningID AND CONVERT(VARCHAR(8),X.Transdate,112) <= :periode),0) as Jumlah 
                FROM CFMsRekening Y
                Left JOIN CFMsGroupRek Z ON Y.GroupRekID=Z.GroupRekID 
                ) as P GROUP BY P.Tipe,P.FgTipe
                ) as L ON K.Tipe=L.Tipe AND K.FgTipe=L.FgTipe 
                WHERE K.FgTipe=:fgtipe 
                ORDER BY K.FgTipe,K.Tipe",
                [
                    'periode' => $param['periode'],
                    'fgtipe' => $row->fgtipe,
                ]
            );
        
            foreach ($cekdata as $hasil)
            {
            
                $cekdetail = DB::select(
                    "SELECT X.GroupRekID as grouprekid,X.GroupRekName as grouprekname,X.Total as amount FROM (
                    SELECT P.GroupRekID,Q.GroupRekName,CASE WHEN P.FgTipe='A' THEN SUM(P.Jumlah) ELSE SUM(P.Jumlah*-1) END as Total FROM (
                    SELECT CASE WHEN z.fgtipe IN (4,5,6,7,8) THEN (select grouprekid from cfmsrekening where rekeningid =(select drlaba from setrekening)) 
                    ELSE Y.GroupRekID END as GroupRekID,
                    CASE WHEN z.fgTipe IN (4,5,6,7,8) THEN 3 ELSE z.fgTipe END as Tipe,case when z.fgtipe in (1,9) then 'A' else 'P' end as FgTipe,
                    ISNULL((SELECT SUM(CASE WHEN X.Jenis='D' THEN X.Amount ELSE X.Amount*-1 END) FROM (
                    $addquery
                    ) as X 
                    WHERE X.RekeningID=Y.RekeningID AND CONVERT(VARCHAR(8),X.Transdate,112) <= :periode),0) as Jumlah 
                    FROM CFMsRekening Y
                    left join CFMsGroupRek Z on Y.GroupRekID=Z.GroupRekID 
                    
                    ) as P INNER JOIN CFMsGroupRek Q ON P.GroupRekID=Q.GroupRekID 
                    WHERE P.Tipe=:tipe AND P.FgTipe=:fgtipe
                    GROUP BY P.GroupRekID,Q.GroupRekName,P.FgTipe ) as X WHERE X.Total <> 0 ORDER BY X.GroupRekID ",
                    [
                        'periode' => $param['periode'],
                        'tipe' => $hasil->tipe,
                        'fgtipe' => $hasil->fgtipe
                    ]
                );

                $hasil->detail = $cekdetail;
                
            }

            $row->header = $cekdata;
        }

        return $result;
    }

}

?>

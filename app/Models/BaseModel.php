<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    public function autoNumber($namatable, $namafield, $formatso, $formatnumber)
    {
        $autonumber = DB::selectOne(
            "select '" . $formatso . "'+FORMAT(ISNULL((select top 1 RIGHT(" . $namafield . "," . strlen($formatnumber) . ") from " . $namatable . " 
            where " . $namafield . " like '%" . $formatso . "%' order by " . $namafield . " desc),0)+1,'" . $formatnumber . "') as nomor "

            // from ".$namatable." where ".$namafield." like '%".$formatso."%'"
        );

        return $autonumber->nomor;
    }

    public function queryAccounting()
    {
        $result =
            "SELECT a.rekeningid,b.transdate,a.jenis,isnull(a.amount,0) as amount,b.currid,b.rate,a.voucherid,a.note from cftrkkbbdt a inner join cftrkkbbhd b on a.voucherid=b.voucherid and b.fgpayment='t' union all 
            select b.rekeningid,a.transdate,case when a.flagkkbb in ('km','bm','ark','arb','arc') then 'd' 
            when a.flagkkbb in ('kk','bk','apk','apb','apc') then 'k' 
            when (select x.flagkkbb from cftrkkbbhd x where x.voucherid=a.idvoucher) in ('km','bm','ark','arb','arc') then 'd' 
            when (select x.flagkkbb from cftrkkbbhd x where x.voucherid=a.idvoucher) in ('kk','bk','apk','apb','apc') then 'k' end,
            isnull(case when a.flagkkbb in ('km','bm','ark','arb','arc') then jumlahd when a.flagkkbb in ('kk','bk','apk','apb','apc') then jumlahk 
            when (select x.flagkkbb from cftrkkbbhd x where x.voucherid=a.idvoucher) in ('km','bm','ark','arb','arc') then jumlahd 
            when (select x.flagkkbb from cftrkkbbhd x where x.voucherid=a.idvoucher) in ('kk','bk','apk','apb','apc') then jumlahk end,0),a.currid,a.rate,a.voucherid,a.note from cftrkkbbhd a 
            inner join cfmsbank b on a.bankid=b.bankid where a.fgpayment='t' and a.fgpayment='t' union all 
            select (select drkas from samsset),transdate,case when flagkkbb in ('km','bm','ark','arb','arc') then 'd' when flagkkbb in ('kk','bk','apk','apb','apc') then 'k' end,
            isnull(case when flagkkbb in ('km','bm','ark','arb','arc') then jumlahd when flagkkbb in ('kk','bk','apk','apb','apc') then jumlahk end,0),currid,rate,voucherid,note from cftrkkbbhd 
            where flagkkbb in ('km','kk','ark','apk') and fgpayment='t' union all 

            select rekeningu,transdate,'d',isnull(ttlpj,0),currid,rate,saleid,saleid from artrpenjualanhd union all 
            select rekeningk,transdate,'k',isnull(case when fgform='ar' then stpj-dp else dp end,0),currid,rate,saleid,saleid from artrpenjualanhd union all 
            select rekeningp,transdate,'k',isnull(ppn,0),currid,rate,saleid,saleid from artrpenjualanhd union all 
            select rekhpp,transdate,'d',isnull(hpp,0),currid,rate,saleid,saleid from artrpenjualanhd union all 
            select rekpersediaan,transdate,'k',isnull(hpp,0),currid,rate,saleid,saleid from artrpenjualanhd union all 

            select rekeningu,transdate,'k',isnull(ttlpb,0),currid,rate,purchaseid,purchaseid from aptrpurchasehd union all 
            select rekpersediaan,transdate,'d',case when fgtax='y' then isnull(ttlpb/(1+(rate*0.01)),0) else isnull(ttlpb,0) end,currid,rate,purchaseid,purchaseid from aptrpurchasehd union all 
            select rekhpp,transdate,'k',case when fgtax='y' then isnull(ttlpb/(1+(rate*0.01)),0) else isnull(ttlpb,0) end,currid,rate,purchaseid,purchaseid from aptrpurchasehd union all 
            select rekeningk,transdate,'d',isnull(ttlpb-ppn,0),currid,rate,purchaseid,purchaseid from aptrpurchasehd union all 
            select rekeningp,transdate,'d',isnull(ppn,0),currid,rate,purchaseid,purchaseid from aptrpurchasehd ";

        return $result;
    }
}

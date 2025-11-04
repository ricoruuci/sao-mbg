<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    public function autoNumber($namatable, $namafield, $formatso, $formatnumber)
    {
        $autonumber = DB::selectOne(
            "select '".$formatso."'+FORMAT(ISNULL((select top 1 RIGHT(".$namafield.",".strlen($formatnumber).") from ".$namatable."
            where ".$namafield." like '%".$formatso."%' order by ".$namafield." desc),0)+1,'".$formatnumber."') as nomor "

            // from ".$namatable." where ".$namafield." like '%".$formatso."%'"
        );

        return $autonumber->nomor;
    }

    public function queryAccounting($params)
    {

        $addCon1 = ''; // default kosong
        $addCon2 = '';
        $addCon3 = '';
        $addCon4 = '';
        $addCon5 = '';
        $addCon6 = '';
        $addCon7 = '';
        $addCon8 = '';
        $addCon9 = '';
        $addCon10 = '';
        $addCon11 = '';

        if (!empty($params['company_id']) && $params['company_id'] != 0)
        {
            $companyId = $params['company_id'];

            $addCon1 = "and b.company_id = $companyId ";
            $addCon2 = "and a.company_id = $companyId ";
            $addCon3 = "and a.company_id = $companyId ";
            $addCon4 = "and company_id = $companyId ";
            $addCon5 = "and company_id = $companyId ";
            $addCon6 = "and company_id = $companyId ";
            $addCon7 = "and a.company_id = $companyId ";
            $addCon8 = "and company_id = $companyId ";
            $addCon9 = "where company_id = $companyId ";
            $addCon10 = "where company_id = $companyId ";
            $addCon11 = "where company_id = $companyId ";
        }

        $default = DB::selectOne("select drkas,drar,drap,drtaxpb,drtaxpj,drpb,drpj,drlaba from setrekening");

        $rek_kas = "'".$default->drkas."'" ?? '';
        $rek_ar = "'".$default->drar."'" ?? '';
        $rek_ap = "'".$default->drap."'" ?? '';
        $rek_taxpb = "'".$default->drtaxpb."'" ?? '';
        $rek_taxpj = "'".$default->drtaxpj."'" ?? '';
        $rek_pb = "'".$default->drpb."'" ?? '';
        $rek_pj = "'".$default->drpj."'" ?? '';
        $rek_laba = "'".$default->drlaba."'" ?? '';

        $result =
            "SELECT a.rekeningid,b.transdate,a.jenis,isnull(a.amount,0) as amount,'IDR' as currid,1 as rate,'T' as fgpayment,a.voucherid,a.note
            from cftrkkbbdt a
            inner join cftrkkbbhd b on a.voucherid=b.voucherid
            where b.FlagKKBB IN ('KM','KK','BM','BK','APB','APK','JU')
            $addCon1
            union all

            select b.rekeningid,a.transdate,case when a.flagkkbb in ('BM') then 'D' else 'K' end,a.total,'IDR' as currid,1 as rate,'T' as fgpayment,a.voucherid,a.note
            from cftrkkbbhd a
            inner join cfmsbank b on a.bankid=b.bankid
            where a.FlagKKBB IN ('BM','BK','APB')
            $addCon2
            union all

            select $rek_kas,a.transdate,case when a.flagkkbb in ('KM') then 'D' else 'K' end,a.total,'IDR' as currid,1 as rate,'T' as fgpayment,a.voucherid,a.note
            from cftrkkbbhd a
            where a.flagkkbb in ('KM','KK','APK')
            $addCon3
            union all

            select $rek_ar,tgljual,'d',isnull(TTLPj,0),'IDR',1,'T',nota,nota from TrJualHd where fgbatal='T' $addCon4 union all
            select $rek_taxpj,tgljual,'k',isnull(TTLTax,0),'IDR',1,'T',nota,nota from TrJualHd where fgbatal='T' $addCon5 union all
            select $rek_pj,tgljual,'k',isnull(STPj,0),'IDR',1,'T',nota,nota from TrJualHd where fgbatal='T' $addCon6 union all
            /*
            select case when a.PayType=3 then $rek_kas else b.RekeningID end,a.tgljual,'d',isnull(a.TTLPj,0),'IDR',1,'T',a.nota,a.nota from TrJualHd a
            left join CFMsBank b on a.BankId=b.bankid
            where a.fgbayar='Y' and a.fgbatal='T' $addCon7 union all
            select $rek_ar,tgljual,'K',isnull(TTLPj,0),'IDR',1,'T',nota,nota from TrJualHd
            where fgbayar='Y' and fgbatal='T' $addCon8 union all
            */
            select $rek_pb,TglBeli,'d',isnull(STPb,0),'IDR',1,'T',nota,nota from TrBeliBBHd $addCon9 union all
            select $rek_taxpb,TglBeli,'d',isnull(TTLTax,0),'IDR',1,'T',nota,nota from TrBeliBBHd $addCon10 union all
            select $rek_ap,TglBeli,'k',isnull(TTLPb,0),'IDR',1,'T',nota,nota from TrBeliBBHd $addCon11 ";

        return $result;
    }

}

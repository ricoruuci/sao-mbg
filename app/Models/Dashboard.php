<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class Dashboard extends Model
{
    function getListData()
    {

        $result = DB::select(
            "WITH calendar as (
            select cast(convert(varchar(4), 2024, 112) + '0101' as date) as date union all
            select dateadd(day, 1, date) from calendar
            where dateadd(day, 1, date) <= cast(convert(varchar(4), getdate(), 112) + '1231' as date)
            )
            select convert(char(8), c.date, 112) as date,isnull(sum(k.ttlpj), 0) as total_sales from calendar c
            left join (
            select convert(date, a.transdate) as transdate, a.ttlpj
            from artrpenjualanhd a
            inner join armssales b on a.salesid = b.salesid
            ) as k on c.date = k.transdate
            where year(c.date) = year(getdate())  and month(c.date) = month(getdate())
            group by 
            convert(char(8), c.date, 112)
            order by 
            date
            option (maxrecursion 0); "
        );

        return $result;
    }

    function gettahun()
    {

        $result = DB::select(
            "SELECT  left(convert(varchar(10), getdate(), 112), 4) as tahun "
        );

        return $result;
    }

    function getRekapJualTahunan()
    {

        $result = DB::select(
            "SELECT 
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0101' and left(convert(varchar(10), getdate(), 112), 4) + '0131' then k.ttlpj else 0 end), 0) as jan,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0201' and left(convert(varchar(10), getdate(), 112), 4) + '0229' then k.ttlpj else 0 end), 0) as feb,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0301' and left(convert(varchar(10), getdate(), 112), 4) + '0331' then k.ttlpj else 0 end), 0) as mar,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0401' and left(convert(varchar(10), getdate(), 112), 4) + '0430' then k.ttlpj else 0 end), 0) as apr,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0501' and left(convert(varchar(10), getdate(), 112), 4) + '0531' then k.ttlpj else 0 end), 0) as may,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0601' and left(convert(varchar(10), getdate(), 112), 4) + '0630' then k.ttlpj else 0 end), 0) as jun,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0701' and left(convert(varchar(10), getdate(), 112), 4) + '0731' then k.ttlpj else 0 end), 0) as jul,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0801' and left(convert(varchar(10), getdate(), 112), 4) + '0831' then k.ttlpj else 0 end), 0) as aug,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '0901' and left(convert(varchar(10), getdate(), 112), 4) + '0930' then k.ttlpj else 0 end), 0) as sep,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '1001' and left(convert(varchar(10), getdate(), 112), 4) + '1031' then k.ttlpj else 0 end), 0) as oct,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '1101' and left(convert(varchar(10), getdate(), 112), 4) + '1130' then k.ttlpj else 0 end), 0) as nov,
            isnull(sum(case when convert(varchar(10), k.transdate, 112) between left(convert(varchar(10), getdate(), 112), 4) + '1201' and left(convert(varchar(10), getdate(), 112), 4) + '1231' then k.ttlpj else 0 end), 0) as des
            from (
            select left(convert(varchar(10), a.transdate, 112), 4) as periode, a.transdate, a.ttlpj 
            from artrpenjualanhd a
            ) as k 
            where k.periode = left(convert(varchar(10), getdate(), 112), 4); "
        );

        return $result;
    }

    function getTotalPO()
    {

        $result = DB::select(
            "SELECT count(gbuid) as total_count,isnull(sum(ttlgbu),0) as total_po from artrpenawaranhd where flag='b' and convert(varchar(12),transdate,112) = convert(varchar(12),getdate(),112) "
        );

        return $result;
    }

    function getTotalSO()
    {

        $result = DB::select(
            "SELECT count(poid) as total_count,isnull(sum(ttlso),0) as total_so from artrpurchaseorderhd where convert(varchar(12),transdate,112) = convert(varchar(12),getdate(),112) "
        );

        return $result;
    }

    function getTotalJual()
    {

        $result = DB::select(
            "SELECT isnull(sum(ttlpj),0) as total_jual from artrpenjualanhd where  convert(varchar(12),transdate,112) = convert(varchar(12),getdate(),112) "
        );

        return $result;
    }

    function getTotalBeli()
    {

        $result = DB::select(
            "SELECT isnull(sum(ttlpb),0) as total_beli from aptrpurchasehd where  convert(varchar(12),transdate,112) = convert(varchar(12),getdate(),112) "
        );

        return $result;
    }


    function getTotalSOPending()
    {

        $result = DB::select(
            "SELECT count(*) as sopending from artrpurchaseorderhd where jenis not in ('Y','X','T') "
        );

        return $result;
    }

    function getUserAktif()
    {

        $result = DB::select(
            "SELECT count(*) as user_active from personal_access_tokens where expires_at>getdate()"
        );

        return $result;
    }
}

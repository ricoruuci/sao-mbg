<?php

namespace App\Models\CF\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class RptKKBB extends Model
{
    function getLaporanKas($param)
    {
        // $cekgroup = DB::selectOne("select dgrpj,dgrpll,dgrpb,dgrbi from samsset");
        // $grouprek = "'".$cekgroup->dgrpj."','".$cekgroup->dgrpll."'";

        $result = DB::select(
            "SELECT 'KAS' as kas,
            isnull(sum(case when B.transdate< :dari1 then (case when a.jenis = 'D'
			then a.amount else a.amount*-1 end) else 0 end),0) as awal,
            isnull(sum(case when B.transdate between :dari2 and :sampai1 then (case when a.jenis = 'D'
			then a.amount else 0 end) else 0 end),0) as debet,
            isnull(sum(case when B.transdate between :dari3 and :sampai2 then (case when a.jenis = 'D'
			then 0 else a.amount end) else 0 end),0) as kredit,
            isnull(sum(case when B.transdate<= :sampai3 then (case when a.jenis = 'D'
			then a.amount else a.amount*-1 end) else 0 end),0) as akhir
            from CFTrKKBBHd B
			INNER JOIN CFTrKKBBDt A ON A.VoucherID=B.VoucherID
            where convert(varchar(8),transdate,112) <= :sampai4
            and B.FlagKKBB in('KM','KK','ARK','APK') ",
            [
                'dari1' => $param['dari'],
                'dari2' => $param['dari'],
                'dari3' => $param['dari'],
                'sampai1' => $param['sampai'],
                'sampai2' => $param['sampai'],
                'sampai3' => $param['sampai'],
                'sampai4' => $param['sampai']
            ]
        );

        $endBalance = 0;

        foreach ($result as $row) {
            $endBalance = $row->awal;

            $cekdata = DB::select(
                "SELECT a.transdate,a.voucherno,c.note,c.amount,c.jenis as jenis
                from cftrkkbbhd a left join cfmsbank b on a.bankid=b.bankid
				inner join cftrkkbbdt c on a.voucherid=c.voucherid
                where convert(varchar(8),transdate,112) between :dari and :sampai  and a.flagkkbb in ('km','kk')
                order by a.transdate,a.voucherno ",
                [
                    'dari' => $param['dari'],
                    'sampai' => $param['sampai']
                ]
            );

            foreach ($cekdata as $hasil) {
                if ($hasil->jenis == 'd') {
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

    function getLaporanBank($param)
    {

        if ($param['bankid']) {
            $addCon = "and c.bankid='" . $param['bankid'] . "'";
        } else {
            $addCon = '';
        }

        $result = DB::select(
            "SELECT c.bankid,c.bankname,
            isnull(sum(case when B.transdate< :dari1 then (case when a.jenis = 'D'
			then a.amount else a.amount*-1 end) else 0 end),0) as awal,
            isnull(sum(case when B.transdate between :dari2 and :sampai1 then (case when a.jenis = 'D'
			then a.amount else 0 end) else 0 end),0) as debet,
            isnull(sum(case when B.transdate between :dari3 and :sampai2 then (case when a.jenis = 'D'
			then 0 else a.amount end) else 0 end),0) as kredit,
            isnull(sum(case when B.transdate<= :sampai3 then (case when a.jenis = 'D'
			then a.amount else a.amount*-1 end) else 0 end),0) as akhir
            from CFTrKKBBHd B
			INNER JOIN CFTrKKBBDt A ON A.VoucherID=B.VoucherID
            left join cfmsbank c on c.bankid=b.bankid
            where convert(varchar(8),transdate,112) <= :sampai4 $addCon
            and B.FlagKKBB in('BM','BK','ARB','APB')
            group by c.bankid,c.bankname",
            [
                'dari1' => $param['dari'],
                'dari2' => $param['dari'],
                'dari3' => $param['dari'],
                'sampai1' => $param['sampai'],
                'sampai2' => $param['sampai'],
                'sampai3' => $param['sampai'],
                'sampai4' => $param['sampai']
            ]
        );

        $endBalance = 0;

        foreach ($result as $row) {
            $endBalance = $row->awal;

            $cekdata = DB::select(
                "SELECT a.transdate,a.voucherno,b.note,b.amount,b.jenis as jenis
                from cftrkkbbhd a left join cfmsbank c on a.bankid=c.bankid
				inner join cftrkkbbdt b on a.voucherid=b.voucherid
                where convert(varchar(8),transdate,112) between :dari and :sampai $addCon  and a.flagkkbb in ('BM','BK','ARB','APB')
                order by a.transdate,a.voucherno ",
                [
                    'dari' => $param['dari'],
                    'sampai' => $param['sampai']
                ]
            );

            foreach ($cekdata as $hasil) {
                if ($hasil->jenis == 'd') {
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
}

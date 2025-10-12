<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class RptPenjualan extends BaseModel
{
    use HasFactory;

    function getLapPenjualan($params)
    {
        $condition = '';

        if (!empty($params['company_id'])) 
        {
            $condition = " and a.company_id=:company_id";
            $bindings = [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'search_keyword' => '%'.$params['search_keyword'].'%',
                'company_id' => $params['company_id']
            ];
        }
        else
        {
            $bindings = [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'search_keyword' => '%'.$params['search_keyword'].'%'
            ];
        }

        $result = DB::select(
            "SELECT a.nota as nota_jual,a.tgljual as transdate,a.nomeja as nomor_meja,
            case when a.fgbayar='Y' then 'LUNAS' else 'BELUM LUNAS' end as status_bayar,a.paytype as payment_type,
            case when a.PayType=0 then 'QRIS' when a.PayType=1 then 'DEBIT CARD' 
                 when a.paytype=2 then 'CREDIT CARD' when a.paytype=3 then 'CASH' end as payment_type_name,
            a.upduser,a.upddate,a.stpj as sub_total,a.ttltax as total_ppn,a.ttlpj as grand_total
            from trjualhd a
            where convert(varchar(10),a.tgljual,112) between :dari and :sampai and a.nota like :search_keyword 
            $condition
            order by a.tgljual,a.nota ",
            $bindings
        );

        return $result;
    }

    function getLapPenjualanHarian($params)
    {
        $total_per_group = 0;
        $total_per_menu = 0;
        $total_payment = 0;
        
        $condition = '';
        $condition2 = '';

        if (!empty($params['company_id'])) 
        {
            $condition = " and b.company_id=:company_id";
            $condition2 = " and a.company_id=:company_id";
            $bindings = [
                'transdate' => $params['transdate'],
                'company_id' => $params['company_id']
            ];
        }
        else
        {
            $bindings = [
                'transdate' => $params['transdate']
            ];
        }

        $per_group = DB::select(
            "SELECT c.kdgroupmenu,d.nmgroupmenu,isnull(sum(a.jumlah),0) as qty,isnull(sum(a.jumlah*a.harga),0) as total from trjualdt a 
            left join trjualhd b on a.nota=b.nota and b.fgbatal='t'
            left join msmenuhd c on a.kdmenu=c.kdmenu
            left join msgroupmenu d on c.kdgroupmenu=d.kdgroupmenu
            where convert(varchar(10),b.tgljual,112) = :transdate and b.fgbatal='T' $condition 
            group by c.kdgroupmenu,d.nmgroupmenu
            order by c.kdgroupmenu ",
            $bindings
        );

        foreach ($per_group as $group) {
            $total_per_group += $group->total;
        }

        $per_menu = DB::select(
            "SELECT c.kdgroupmenu,a.kdmenu,c.nmmenu,isnull(sum(a.jumlah),0) as qty,a.harga,isnull(sum(a.jumlah*a.harga),0) as total from trjualdt a 
            left join trjualhd b on a.nota=b.nota and b.fgbatal='t'
            left join msmenuhd c on a.kdmenu=c.kdmenu
            where convert(varchar(10),b.tgljual,112) = :transdate and b.fgbatal='T' $condition
            group by c.kdgroupmenu,a.kdmenu,c.NmMenu,a.harga
            order by c.kdgroupmenu,c.NmMenu,a.kdmenu,a.harga ",
            $bindings
        );

        foreach ($per_menu as $menu) {
            $total_per_menu += $menu->total;
        }

        $count_transaksi = DB::selectOne(
            "SELECT isnull(sum(1),0) as total,
            isnull(sum(case when a.fgbatal='T' then (case when a.fgbayar='Y' then 1 else 0 end) else 0 end),0) as lunas,
            isnull(sum(case when a.fgbatal='T' then (case when a.fgbayar='T' then 1 else 0 end) else 0 end),0) as blm_lunas,
            isnull(sum(case when a.fgbatal='Y' then 1 else 0 end),0) as batal
            from trjualhd a
            where convert(varchar(10),a.tgljual,112) = :transdate $condition2 ",
            $bindings
        );

        $payment = DB::select(
            "SELECT case when a.fgbayar='T' then 'X' else cast(a.paytype as varchar(10)) end as payment_type,
            CASE WHEN a.FgBayar='Y' THEN 
            (case when a.paytype=0 then 'QRIS' when a.paytype=1 then 'DEBIT' when a.paytype=2 then 'CREDIT' else 'CASH' end)
            ELSE 'BELUM BAYAR'
            end as payment_type_name,
            isnull(sum(a.ttlpj),0) as total 
            from TrJualHd a 
            where  a.FgBatal='T' and a.fgbayar='Y'
            and convert(varchar(10),a.tgljual,112) = :transdate $condition2
            group by a.paytype,a.fgbayar ",
            $bindings
        );

        foreach ($payment as $pay) {
            $total_payment += $pay->total;
        }
        
        $result = [
            'per_group' => [
                'detail' => $per_group, 
                'total_per_group' => $total_per_group
            ],
            'per_menu' => [
                'detail' => $per_menu, 
                'total_per_menu' => $total_per_menu
            ],
            'count_transaksi' => $count_transaksi,
            'payment' => [
                'detail' => $payment, 
                'total_payment' => $total_payment
            ]
        ];

        return $result;
    }

}

?>

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class RptInventory extends BaseModel
{
    use HasFactory;

    function getLapStock($params)
    {
        $showAll = $params['show_zero'] ?? 'T';
        $condition = '';

        if ($showAll == 'T') {
            $condition = " AND k.stock <> 0 ";
        }

        if (!empty($params['company_id'])) 
        {
            $condition = $condition . " and k.company_id=:company_id ";
            $bindings = [
                            'transdate' => $params['transdate'],
                            'search_keyword' => '%'.$params['search_keyword'].'%',
                            'search_keyword2' => '%'.$params['search_keyword'].'%',
                            'company_id' => $params['company_id']
            ];
        }
        else
        {
            $bindings = [
                            'transdate' => $params['transdate'],
                            'search_keyword' => '%'.$params['search_keyword'].'%',
                            'search_keyword2' => '%'.$params['search_keyword'].'%'
            ];
        }

        $result = DB::select(
            "SELECT k.itemid as bahan_baku_id,l.nmbb as bahan_baku_name,k.stock,k.hpp,l.satkecil as satuan,k.stock*k.hpp as total_hpp from (
            select a.itemid,isnull(sum(case when a.fgtrans<50 then a.qty else a.qty*-1 end),0) as stock,a.warehouseid,a.hpp,a.company_id from allitem a
            where convert(varchar(10),a.transdate,112) <= :transdate
            group by a.itemid,a.hpp,a.company_id,a.warehouseid
            ) as k
            left join msbahanbaku l on k.itemid=l.kdbb
            where (k.itemid like :search_keyword or l.nmbb like :search_keyword2)
            $condition
            order by l.nmbb",
            $bindings
        );

        $totalHpp   = 0;

        foreach ($result as $row) {
            $totalHpp   += $row->total_hpp;
        }

        return [
            'detail' => $result,
            'total' => [
                'total_hpp'   => $totalHpp
            ]
        ];
    }

    function getLapKartuStock($params)
    {   
        $condition = '';

        if (!empty($params['company_id'])) 
        {
            $condition = $condition . " and a.company_id=:company_id ";
            $bindings1 = [
                'dari1' => $params['dari'],
                'dari2' => $params['dari'],
                'dari3' => $params['dari'],
                'sampai1' => $params['sampai'],
                'sampai2' => $params['sampai'],
                'sampai3' => $params['sampai'],
                'sampai4' => $params['sampai'],
                'bahan_baku_id' => '%' . $params['search_keyword'] . '%',
                'company_id' => $params['company_id']
            ];
        }
        else
        {
            $bindings1 = [
                'dari1' => $params['dari'],
                'dari2' => $params['dari'],
                'dari3' => $params['dari'],
                'sampai1' => $params['sampai'],
                'sampai2' => $params['sampai'],
                'sampai3' => $params['sampai'],
                'sampai4' => $params['sampai'],
                'bahan_baku_id' => '%' . $params['search_keyword'] . '%'
            ];
        }

        $headers = DB::select(
            "SELECT x.itemid as bahan_baku_id,x.nmbb as bahan_baku_name,x.satuan,
                    x.stock_awal,x.stock_masuk,x.stock_keluar,x.stock_akhir 
            FROM (
                SELECT k.itemid, l.NmBB, l.satkecil AS satuan,
                    ISNULL(SUM(CASE WHEN CONVERT(varchar(10),k.transdate,112) < :dari1 
                                    THEN (CASE WHEN k.fgtrans<50 THEN k.qty ELSE k.qty*-1 END) 
                                    ELSE 0 END), 0) AS stock_awal,
                    ISNULL(SUM(CASE WHEN CONVERT(varchar(10),k.transdate,112) BETWEEN :dari2 AND :sampai1 
                                    THEN (CASE WHEN k.fgtrans<50 THEN k.qty ELSE 0 END) 
                                    ELSE 0 END), 0) AS stock_masuk,
                    ISNULL(SUM(CASE WHEN CONVERT(varchar(10),k.transdate,112) BETWEEN :dari3 AND :sampai2 
                                    THEN (CASE WHEN k.fgtrans>50 THEN k.qty ELSE 0 END) 
                                    ELSE 0 END), 0) AS stock_keluar,
                    ISNULL(SUM(CASE WHEN CONVERT(varchar(10),k.transdate,112) <= :sampai3 
                                    THEN (CASE WHEN k.fgtrans<50 THEN k.qty ELSE k.qty*-1 END) 
                                    ELSE 0 END), 0) AS stock_akhir
                FROM (
                    SELECT a.itemid, a.transdate, a.fgtrans, a.hpp, a.qty
                    FROM allitem a
                    WHERE CONVERT(varchar(10),a.transdate,112) <= :sampai4 $condition
                ) AS k
                LEFT JOIN msbahanbaku l ON k.itemid = l.kdbb
                GROUP BY k.itemid, l.NmBB, l.satkecil
            ) AS x
            WHERE (x.stock_awal + x.stock_masuk + x.stock_keluar + x.stock_akhir) <> 0
            AND x.itemid LIKE :bahan_baku_id
            ORDER BY x.nmbb",
            $bindings1
        );

        foreach ($headers as &$header) {

            if (!empty($params['company_id'])) 
            {
                $bindings2 = [
                        'dari' => $params['dari'],
                        'sampai' => $params['sampai'],
                        'itemid' => $header->bahan_baku_id,
                        'company_id' => $params['company_id']
                ];
            }
            else
            {
                $bindings2 = [
                        'dari' => $params['dari'],
                        'sampai' => $params['sampai'],
                        'itemid' => $header->bahan_baku_id
                ];
            }
            
            $details = DB::select(
                "SELECT 
                    k.transdate,
                    k.itemid AS bahan_baku_id,
                    l.nmbb AS bahan_baku_name,
                    k.voucherno AS nomor_voucher,
                    k.fgtrans AS status_code,
                    k.fgtransname AS status_name,
                    k.qty
                FROM (
                    SELECT a.voucherno, a.itemid, a.transdate, a.fgtrans, a.hpp, a.qty,
                        CASE WHEN a.fgtrans < 50 THEN 'BELI' ELSE 'JUAL' END AS fgtransname
                    FROM allitem a 
                    WHERE CONVERT(varchar(10), a.transdate,112) BETWEEN :dari AND :sampai $condition
                ) AS k
                LEFT JOIN msbahanbaku l ON k.itemid = l.kdbb
                WHERE k.itemid = :itemid
                ORDER BY k.transdate, k.voucherno, k.fgtrans",
                $bindings2
            );

            // Tambahkan running stock berdasarkan stock_awal
            $running = $header->stock_awal;
            foreach ($details as &$row) {
                if ($row->status_code < 50) {
                    $running += $row->qty;
                } else {
                    $running -= $row->qty;
                }
                $row->running_stock = $running;
            }

            $header->detail = $details;
        }

        return $headers;
    }


}

?>

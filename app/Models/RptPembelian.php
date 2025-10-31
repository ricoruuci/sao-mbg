<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class RptPembelian extends BaseModel
{
    use HasFactory;

    function getLapPembelian($params)
    {
        $condition = '';

        if (!empty($params['company_id']))
        {
            $condition = " and a.company_id=:company_id";
            $bindings = [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'search_keyword' => '%'.$params['search_keyword'].'%',
                'supplier_keyword' => '%'.$params['supplier_keyword'].'%',
                'company_id' => $params['company_id']
            ];
            // dd(var_dump($params['company_id']));
        }
        else
        {
            $bindings = [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'search_keyword' => '%'.$params['search_keyword'].'%',
                'supplier_keyword' => '%'.$params['supplier_keyword'].'%'
            ];
        }

        $result = DB::select(
            "SELECT a.nota as nota_beli,a.KdSupplier as supplier_id,b.NmSupplier as supplier_name,a.tglbeli as transdate,
            a.Tax as ppn,a.stpb as sub_total,a.ttltax as total_ppn,a.TTLPb as grand_total,a.upddate,a.upduser
            from TrBeliBBHd a inner join MsSupplier b on a.KdSupplier=b.KdSupplier
            where convert(varchar(10),a.tglbeli,112) between :dari and :sampai and a.nota like :search_keyword
            and isnull(b.nmSupplier,'') like :supplier_keyword
            $condition
            order by a.tglbeli,a.nota ",
            $bindings
        );

        return $result;
    }

    function getLapHutang($params)
    {
        $condition = '';

        if (!empty($params['company_id']))
        {
            $condition = " and a.company_id=:company_id";
            $bindings = [
                'tgl1' => $params['transdate'],
                'tgl2' => $params['transdate'],
                'search_keyword' => '%'.$params['search_keyword'].'%',
                'supplier_keyword' => '%'.$params['supplier_keyword'].'%',
                'supplier_keyword2' => '%'.$params['supplier_keyword'].'%',
                'company_id' => $params['company_id']
            ];
        }
        else
        {
            $bindings = [
                'tgl1' => $params['transdate'],
                'tgl2' => $params['transdate'],
                'search_keyword' => '%'.$params['search_keyword'].'%',
                'supplier_keyword' => '%'.$params['supplier_keyword'].'%',
                'supplier_keyword2' => '%'.$params['supplier_keyword'].'%'
            ];
        }

        $result = DB::select(
            "SELECT k.*, k.total - k.bayar AS sisa
            FROM (
                SELECT
                    a.nota AS nota_beli,
                    a.kdsupplier AS supplier_id,
                    b.nmsupplier AS supplier_name,
                    a.tglbeli AS transdate,
                    ISNULL((
                        SELECT SUM(CASE WHEN x.jenis = 'D' THEN x.amount ELSE x.amount * -1 END)
                        FROM cftrkkbbdt x
                        INNER JOIN cftrkkbbhd y ON x.voucherid = y.voucherid
                        WHERE x.note = a.nota AND CONVERT(VARCHAR(10), y.transdate, 112) <= :tgl1
                    ), 0) AS bayar,
                    a.ttlpb AS total
                FROM trbelibbhd a
                INNER JOIN mssupplier b ON a.kdsupplier = b.kdsupplier
                WHERE CONVERT(VARCHAR(10), a.tglbeli, 112) <= :tgl2 $condition
            ) AS k
            WHERE k.total - k.bayar <> 0
            AND k.nota_beli LIKE :search_keyword
            AND (
                ISNULL(k.supplier_id, '') LIKE :supplier_keyword
                OR ISNULL(k.supplier_name, '') LIKE :supplier_keyword2
            )
            ORDER BY k.transdate, k.nota_beli ",
            $bindings
        );

        return $result;
    }


    public function getLaporanBeliAdjustment($params)
    {
        if (empty($params['adjustment']))
        {
            $adjustment = 0;
        }
        else
        {
            $adjustment = $params['adjustment'];
        }

        $result = DB::select(
            "SELECT b.company_id,e.company_code,e.company_name,e.company_address,b.tglbeli as transdate,b.nota as nota_beli,b.kdsupplier as supplier_id,
            c.nmsupplier as supplier_name,a.kdbb as bahan_baku_id,d.nmbb as bahan_baku_name,a.jml as qty,a.kdsat as satuan,a.harga as price,
            isnull(a.jml*a.harga,0) as total,
            (case when isnull(b.fg_upload,'T')='T' then $adjustment else isnull(b.interest,0) end) as adjustment,
            (a.jml+round(a.jml*((case when isnull(b.fg_upload,'T')='T' then $adjustment else isnull(b.interest,0) end)*0.01),0) ) as qty_adjustment,
            (a.jml+round(a.jml*((case when isnull(b.fg_upload,'T')='T' then $adjustment else isnull(b.interest,0) end)*0.01),0) )*a.harga as total_adjustment
            from trbelibbdt a
            inner join trbelibbhd b on a.nota=b.nota
            inner join mssupplier c on b.kdsupplier=c.kdsupplier
            inner join msbahanbaku d on a.kdbb=d.kdbb
            inner join mscabang e on b.company_id=e.company_id
            where convert(varchar(10),b.tglbeli,112) between :dari and :sampai
            and b.company_id=:company_id
            order by b.tglbeli,b.nota,d.nmbb ",
            [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'company_id' => $params['company_id']
            ]
        );

        return $result;
    }

    public function updateFgUploadNota($params)
    {
        $result = DB::update(
            "UPDATE trbelibbhd set fg_upload='Y', interest=:adjustment where nota=:nota_beli ",
            [
                'nota_beli' => $params['nota_beli'],
                'adjustment' => $params['adjustment']
            ]
        );

        return $result;
    }

}

?>

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class BeliHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trbelibbhd';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT trbelibbhd (nota,kdsupplier,tglbeli,tax,keterangan,upddate,upduser,ttlpb,stpb,ttltax,company_id,interest,fg_upload,discamount)
            VALUES (:nota, :kdsupplier, :transdate, :tax, :note, getdate(), :upduser, 0, 0, 0,:company_id, :interest, :fgupload, :discamount)",
            [
                'nota' => $params['nota_beli'],
                'kdsupplier' => $params['supplier_id'],
                'transdate' => $params['transdate'],
                'tax' => $params['ppn'],
                'note' => $params['note'],
                'upduser' => $params['upduser'],
                'company_id' => $params['company_id'],
                'interest' => 0,
                'fgupload' => 'T',
                'discamount' => $params['discamount']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE trbelibbhd
            SET
            tglbeli = :transdate,
            tax = :tax,
            keterangan = :note,
            upddate = getdate(),
            upduser = :upduser,
            kdsupplier = :kdsupplier,
            discamount = :discamount
            WHERE nota = :nota",
            [
                'nota' => $params['nota_beli'],
                'transdate' => $params['transdate'],
                'tax' => $params['ppn'],
                'note' => $params['note'],
                'upduser' => $params['upduser'],
                'kdsupplier' => $params['supplier_id'],
                'discamount' => $params['discamount']
            ]
        );

        return $result;
    }

    function getAllData($params)
    {
        $addCon = ''; // default kosong

        if (!empty($params['company_id']))
        {
            $addCon = 'and a.company_id =:company_id ';
            $binding = [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'company_id' => $params['company_id'],
                'searchkeyword' => '%'.$params['search_keyword'].'%',
                'supplierkeyword' => '%'.$params['supplier_keyword'].'%'
            ];
        }
        else
        {
            $binding = [
                'dari' => $params['dari'],
                'sampai' => $params['sampai'],
                'searchkeyword' => '%'.$params['search_keyword'].'%',
                'supplierkeyword' => '%'.$params['supplier_keyword'].'%'
            ];
        }

        $result = DB::select(
            "SELECT a.nota as nota_beli,a.kdsupplier as supplier_id,b.nmsupplier as supplier_name,
            isnull(b.hp,'') as supplier_phone,isnull(b.cp,'') as supplier_pic,
            a.tglbeli as transdate,a.tax as ppn,keterangan as note,a.upddate,a.upduser,
            a.stpb as sub_total,isnull(a.discamount,0) as disc_amount,a.ttltax as total_ppn,a.ttlpb as grand_total,
            isnull(a.fg_upload,'T') as fg_upload,
            case when isnull(a.fg_upload,'T')='T' then 'Belum Upload' else 'Sudah Upload' end as status_upload,
            a.company_id,c.company_code,c.company_name,c.company_address
            from trbelibbhd a
            inner join mssupplier b on a.kdsupplier=b.kdsupplier
            left join mscabang c on a.company_id = c.company_id
            where convert(varchar(10),a.tglbeli,112) between :dari and :sampai
            $addCon
            and isnull(a.nota,'') like :searchkeyword and isnull(b.nmsupplier,'') like :supplierkeyword
            order by a.nota ",
            $binding
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.nota as nota_beli,a.kdsupplier as supplier_id,b.nmsupplier as supplier_name,
            isnull(b.hp,'') as supplier_phone,isnull(b.cp,'') as supplier_pic,
            a.tglbeli as transdate,a.tax as ppn,keterangan as note,a.upddate,a.upduser,
            a.stpb as sub_total,isnull(a.discamount,0) as disc_amount,a.ttltax as total_ppn,a.ttlpb as grand_total,
            isnull(a.fg_upload,'T') as fg_upload,
            case when isnull(a.fg_upload,'T')='T' then 'Belum Upload' else 'Sudah Upload' end as status_upload,
            a.company_id,c.company_code,c.company_name,c.company_address
            from trbelibbhd a
            inner join mssupplier b on a.kdsupplier=b.kdsupplier
            left join mscabang c on a.company_id = c.company_id
            where a.nota = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function getDataByIdAdjustment($id)
    {
        $result = DB::selectOne(
            "SELECT
                a.nota as nota_beli,
                a.kdsupplier as supplier_id,
                b.nmsupplier as supplier_name,
                isnull(b.hp,'') as supplier_phone,
                isnull(b.cp,'') as supplier_pic,
                a.tglbeli as transdate,
                a.tax as ppn,
                a.keterangan as note,
                a.upddate,
                a.upduser,

                -- TOTAL SUBTOTAL SETELAH ADJUSTMENT
                SUM(
                    (CASE WHEN ISNULL(a.fg_upload,'T')='Y'
                        THEN d.jml + ROUND(d.jml * ISNULL(a.interest,0) * 0.01, 0)
                        ELSE d.jml END) * d.harga
                ) as sub_total,

                isnull(a.discamount,0) as disc_amount,
                a.ttltax as total_ppn,

                -- GRAND TOTAL SETELAH ADJUSTMENT
                SUM(
                    (CASE WHEN ISNULL(a.fg_upload,'T')='Y'
                        THEN d.jml + ROUND(d.jml * ISNULL(a.interest,0) * 0.01, 0)
                        ELSE d.jml END) * d.harga
                )
                - isnull(a.discamount,0)
                + a.ttltax as grand_total,

                isnull(a.fg_upload,'T') as fg_upload,
                CASE WHEN isnull(a.fg_upload,'T')='T' THEN 'Belum Upload' ELSE 'Sudah Upload' END as status_upload,

                a.company_id,
                c.company_code,
                c.company_name,
                c.company_address

            FROM trbelibbhd a
            INNER JOIN mssupplier b ON a.kdsupplier=b.kdsupplier
            LEFT JOIN mscabang c ON a.company_id = c.company_id
            LEFT JOIN trbelibbdt d ON a.nota = d.nota AND a.kdsupplier = d.kdsupplier

            WHERE a.nota = :id
            GROUP BY
                a.nota,a.kdsupplier,b.nmsupplier,b.hp,b.cp,a.tglbeli,a.tax,
                a.keterangan,a.upddate,a.upduser,a.discamount,a.ttltax,
                a.fg_upload,a.company_id,c.company_code,c.company_name,c.company_address",
            ['id' => $id]
        );

        return $result;
    }


    function hitungTotal($id)
    {
        $result = DB::selectOne(
            "SELECT k.nota,k.kdsupplier,k.total as sub_total,(k.total-k.discamount)*k.tax*0.01 as total_ppn,
            (k.total-k.discamount)*(1+(k.tax*0.01)) as grand_total from (
            select a.Nota,b.KdSupplier,isnull(sum(a.jml*a.harga),0) as total,b.tax,isnull(b.discamount,0) as discamount
            from TrBeliBBDt a inner join TrBeliBBHd b on a.Nota=b.Nota and a.KdSupplier=b.KdSupplier
            group by a.nota,b.KdSupplier,b.tax,b.discamount
            ) as K
            where k.nota=:id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function updateTotal($params)
    {
        $result = DB::update(
            "UPDATE trbelibbhd
            SET
            stpb = :subtotal,
            ttlpb = :grandtotal,
            ttltax = :pajak
            where nota=:id ",
            [
                'subtotal' => $params['sub_total'],
                'grandtotal' => $params['grand_total'],
                'pajak' => $params['total_ppn'],
                'id' => $params['nota_beli']
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            "SELECT nota,company_id,isnull(fg_upload,'T') as fg_upload from trbelibbhd WHERE nota = :id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($transdate,$company_code)
    {
        $tahunBulan = '/' . substr($transdate, 2, 2) . '/' . substr($transdate, 4, 2) . '/';

        $autoNumber = $this->autoNumber($this->table, 'nota', $company_code.$tahunBulan, '000');

        return $autoNumber;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM trbelibbhd where nota = :nota",
            [
                'nota' => $id
            ]
        );

        return $result;
    }
}

?>

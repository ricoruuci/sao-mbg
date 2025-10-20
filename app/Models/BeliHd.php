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
            "INSERT trbelibbhd (nota,kdsupplier,tglbeli,tax,keterangan,upddate,upduser,ttlpb,stpb,ttltax,company_id)
            VALUES (:nota, :kdsupplier, :transdate, :tax, :note, getdate(), :upduser, 0, 0, 0,:company_id)",
            [
                'nota' => $params['nota_beli'],
                'kdsupplier' => $params['supplier_id'],
                'transdate' => $params['transdate'],
                'tax' => $params['ppn'],
                'note' => $params['note'],
                'upduser' => $params['upduser'],
                'company_id' => $params['company_id']
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
            kdsupplier = :kdsupplier
            WHERE nota = :nota",
            [
                'nota' => $params['nota_beli'],
                'transdate' => $params['transdate'],
                'tax' => $params['ppn'],
                'note' => $params['note'],
                'upduser' => $params['upduser'],
                'kdsupplier' => $params['supplier_id']
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
            a.tglbeli as transdate,a.tax as ppn,keterangan as note,a.upddate,a.upduser,
            a.stpb as sub_total,a.ttltax as total_ppn,a.ttlpb as grand_total from trbelibbhd a
            inner join mssupplier b on a.kdsupplier=b.kdsupplier
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
            a.tglbeli as transdate,a.tax as ppn,keterangan as note,a.upddate,a.upduser,
            a.stpb as sub_total,a.ttltax as total_ppn,a.ttlpb as grand_total from trbelibbhd a
            inner join mssupplier b on a.kdsupplier=b.kdsupplier
            where a.nota = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function hitungTotal($id)
    {
        $result = DB::selectOne(
            "SELECT k.nota,k.kdsupplier,k.total as sub_total,k.total*k.tax*0.01 as total_ppn,k.total*(1+(k.tax*0.01)) as grand_total from (
            select a.Nota,b.KdSupplier,isnull(sum(a.jml*a.harga),0) as total,b.tax
            from TrBeliBBDt a inner join TrBeliBBHd b on a.Nota=b.Nota and a.KdSupplier=b.KdSupplier
            group by a.nota,b.KdSupplier,b.tax
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
            'SELECT nota,company_id from trbelibbhd WHERE nota = :id',
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

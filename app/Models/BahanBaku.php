<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class BahanBaku extends BaseModel
{
    use HasFactory;

    protected $table = 'msbahanbaku';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT a.kdbb as bahan_baku_id, a.nmbb as bahan_baku_name,
            a.satkecil as satuan, a.satbesar as satuan_besar, a.jumsat as konversi,
            b.kdgroupbb as group_bahan_baku_id, b.nmgroupbb as group_bahan_baku_name,a.upddate,a.upduser,
            a.fg_active
            FROM msbahanbaku a
            inner join msgroupbb b on a.kdgroupbb = b.kdgroupbb
            WHERE a.fgform='BB' and (a.nmbb LIKE :search_keyword
            OR b.nmgroupbb LIKE :search_keyword2)
            and (:fg_active = '' or a.fg_active = :fg_active2)
            order by a.nmbb ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%',
                'search_keyword2' => '%' . $params['search_keyword'] . '%',
                'fg_active' => $params['fg_active'],
                'fg_active2' => $params['fg_active'],
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.kdbb as bahan_baku_id, a.nmbb as bahan_baku_name,
            a.satkecil as satuan, a.satbesar as satuan_besar, a.jumsat as konversi,
            b.kdgroupbb as group_bahan_baku_id, b.nmgroupbb as group_bahan_baku_name,a.upddate,a.upduser,
            a.fg_active
            FROM msbahanbaku a
            inner join msgroupbb b on a.kdgroupbb = b.kdgroupbb
            WHERE  a.fgform='BB' and a.kdbb = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekTerpakai($id)
    {
        $result = DB::selectOne(
            'SELECT * from TrBeliBBDt WHERE kdbb = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from msbahanbaku WHERE kdbb = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekDataSatuan($id)
    {
        $result = DB::selectOne(
            "SELECT K.* from (
            SELECT kdbb,satkecil as satuan from msbahanbaku UNION ALL
            SELECT kdbb,satbesar as satuan from msbahanbaku
            ) as K
            WHERE kdbb = :id ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msbahanbaku (kdbb, nmbb, satkecil, kdgroupbb, satbesar, jumsat,upddate,upduser, fgform, fg_active)
            VALUES (:kdbb, :nmbb, :satkecil, :kdgroupbb, :satbesar, :jumsat, getdate(), :upduser, 'BB', :fg_active)",
            [
                'kdbb' => $params['bahan_baku_id'],
                'nmbb' => $params['bahan_baku_name'],
                'satkecil' => $params['satuan'],
                'kdgroupbb' => $params['group_bahan_baku_id'],
                'satbesar' => $params['satuan_besar'],
                'jumsat' => $params['konversi'],
                'upduser' => $params['upduser'],
                'fg_active' => $params['fg_active']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE msbahanbaku SET
                nmbb = :nmbb,
                satkecil = :satkecil,
                kdgroupbb = :kdgroupbb,
                satbesar = :satbesar,
                jumsat = :jumsat,
                upddate = getdate(),
                upduser = :upduser,
                fg_active = :fg_active
            WHERE kdbb = :kdbb",
            [
                'kdbb' => $params['bahan_baku_id'],
                'nmbb' => $params['bahan_baku_name'],
                'satkecil' => $params['satuan'],
                'kdgroupbb' => $params['group_bahan_baku_id'],
                'satbesar' => $params['satuan_besar'],
                'jumsat' => $params['konversi'],
                'upduser' => $params['upduser'],
                'fg_active' => $params['fg_active']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msbahanbaku WHERE kdbb = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'kdbb', 'BB', '0000');

        return $autoNumber;
    }

}

?>

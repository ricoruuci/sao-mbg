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
            "SELECT a.kdbb as bahan_baku_id, a.nmbb as bahan_baku_name, a.satkecil as satuan,
            b.kdgroupbb as group_bahan_baku_id, b.nmgroupbb as group_bahan_baku_name
            FROM msbahanbaku a
            inner join msgroupbb b on a.kdgroupbb = b.kdgroupbb
            WHERE a.nmbb LIKE :search_keyword
            OR b.nmgroupbb LIKE :search_keyword2
            order by a.nmbb ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%',
                'search_keyword2' => '%' . $params['search_keyword'] . '%'

            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.kdbb as bahan_baku_id, a.nmbb as bahan_baku_name, a.satkecil as satuan,
            b.kdgroupbb as group_bahan_baku_id, b.nmgroupbb as group_bahan_baku_name
            FROM msbahanbaku a
            inner join msgroupbb b on a.kdgroupbb = b.kdgroupbb
            WHERE a.kdbb = :id",
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

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msbahanbaku (kdbb, nmbb, satkecil, kdgroupbb)
            VALUES (:kdbb, :nmbb, :satkecil, :kdgroupbb)",
            [
                'kdbb' => $params['bahan_baku_id'],
                'nmbb' => $params['bahan_baku_name'],
                'satkecil' => $params['satuan'],
                'kdgroupbb' => $params['group_bahan_baku_id']
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
            kdgroupbb = :kdgroupbb
            WHERE kdbb = :kdbb",
            [
                'kdbb' => $params['bahan_baku_id'],
                'nmbb' => $params['bahan_baku_name'],
                'satkecil' => $params['satuan'],
                'kdgroupbb' => $params['group_bahan_baku_id']
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class GroupBahanBaku extends BaseModel
{
    use HasFactory;

    protected $table = 'msgroupbb';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT a.kdgroupbb as group_bahan_baku_id, a.nmgroupbb as group_bahan_baku_name, a.upddate, a.upduser
            FROM msgroupbb a
            WHERE a.nmgroupbb LIKE :search_keyword
            order by a.nmgroupbb ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.kdgroupbb as group_bahan_baku_id, a.nmgroupbb as group_bahan_baku_name, a.upddate, a.upduser
            FROM msgroupbb a
            WHERE a.kdgroupbb = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from msgroupbb WHERE kdgroupbb = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekTerpakai($id)
    {
        $result = DB::selectOne(
            'SELECT * from msbahanbaku WHERE kdgroupbb = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msgroupbb (kdgroupbb, nmgroupbb, upddate, upduser)
            VALUES (:kdgroupbb, :nmgroupbb, getdate(), :upduser)",
            [
                'kdgroupbb' => $params['group_bahan_baku_id'],
                'nmgroupbb' => $params['group_bahan_baku_name'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE msgroupbb SET 
            nmgroupbb = :nmgroupbb,
            upddate = getdate(),
            upduser = :upduser
            WHERE kdgroupbb = :kdgroupbb",
            [
                'kdgroupbb' => $params['group_bahan_baku_id'],
                'nmgroupbb' => $params['group_bahan_baku_name'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msgroupbb WHERE kdgroupbb = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'kdgroupbb', 'GBB', '0000');

        return $autoNumber;
    }

}

?>

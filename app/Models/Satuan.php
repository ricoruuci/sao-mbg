<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Satuan extends BaseModel
{
    use HasFactory;

    protected $table = 'mssatuan';

    public $timestamps = false;

    function getAllSatuanBarang($params)
    {
        $result = DB::select(
            "SELECT * from (
            select kdbb as bahan_baku_id,satbesar as satuan from MsBahanBaku union all
            select kdbb,satkecil from MsBahanBaku
            ) as K where isnull(satuan,'')<>'' and satuan like :search_keyword
            order by satuan ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT kdsat as satuan,upduser,upddate from mssatuan where kdsat like :search_keyword
            order by kdsat ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT kdsat as satuan,upduser,upddate from mssatuan where kdsat = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from mssatuan WHERE kdsat = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekTerpakai($id)
    {
        $result = DB::selectOne(
            'SELECT * from msbahanbaku WHERE satkecil = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO mssatuan (kdsat,upddate,upduser)
            VALUES (:kdsat, GETDATE(), :upduser)",
            [
                'kdsat' => $params['satuan'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM mssatuan WHERE kdsat = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

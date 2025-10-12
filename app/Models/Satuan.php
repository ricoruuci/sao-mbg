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

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT kdsat as satuan from mssatuan where kdsat like :search_keyword
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
            "SELECT kdsat as satuan from mssatuan where kdsat = :id",
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
            "INSERT INTO mssatuan (kdsat) 
            VALUES (:kdsat)",
            [
                'kdsat' => $params['satuan']
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

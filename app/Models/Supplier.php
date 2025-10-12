<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Supplier extends BaseModel
{
    use HasFactory;

    protected $table = 'mssupplier';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT kdsupplier as supplier_id,nmsupplier as supplier_name,upddate,upduser 
            from mssupplier 
            where nmsupplier like :search_keyword
            order by nmsupplier ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT kdsupplier as supplier_id,nmsupplier as supplier_name,upddate,upduser 
            from mssupplier 
            where kdsupplier = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from mssupplier WHERE kdsupplier = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO mssupplier (kdsupplier,nmsupplier,upddate,upduser) 
            VALUES (:kdsupplier, :nmsupplier, getdate(), :upduser)",
            [
                'kdsupplier' => $params['supplier_id'],
                'nmsupplier' => $params['supplier_name'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE mssupplier SET 
            nmsupplier = :nmsupplier, 
            upddate = getdate(), 
            upduser = :upduser 
            WHERE kdsupplier = :kdsupplier",
            [
                'kdsupplier' => $params['supplier_id'],
                'nmsupplier' => $params['supplier_name'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM mssupplier WHERE kdsupplier = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'kdsupplier', 'SUP', '0000');

        return $autoNumber;
    }

}

?>

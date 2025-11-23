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
            "SELECT kdsupplier as supplier_id,nmsupplier as supplier_name,isnull(hp,'') as phone,isnull(cp,'') as pic,upddate,upduser,
            isnull(bank_branch,'') as bank_branch, isnull(bank_account,'') as bank_account, isnull(bank_holder,'') as bank_holder
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
            "SELECT kdsupplier as supplier_id,nmsupplier as supplier_name,isnull(hp,'') as phone,isnull(cp,'') as pic,upddate,upduser,
            isnull(bank_branch,'') as bank_branch, isnull(bank_account,'') as bank_account, isnull(bank_holder,'') as bank_holder
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
            "INSERT INTO mssupplier (kdsupplier,nmsupplier,upddate,upduser, hp, cp, bank_branch, bank_account, bank_holder)
            VALUES (:kdsupplier, :nmsupplier, getdate(), :upduser, :hp, :cp, :bank_branch, :bank_account, :bank_holder)",
            [
                'kdsupplier' => $params['supplier_id'],
                'nmsupplier' => $params['supplier_name'],
                'upduser' => $params['upduser'],
                'hp' => $params['phone'],
                'cp' => $params['pic'],
                'bank_branch' => $params['bank_branch'],
                'bank_account' => $params['bank_account'],
                'bank_holder' => $params['bank_holder']
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
                upduser = :upduser,
                hp = :hp,
                cp = :cp,
                bank_branch = :bank_branch,
                bank_account = :bank_account,
                bank_holder = :bank_holder
            WHERE kdsupplier = :kdsupplier",
            [
                'kdsupplier' => $params['supplier_id'],
                'nmsupplier' => $params['supplier_name'],
                'upduser' => $params['upduser'],
                'hp' => $params['phone'],
                'cp' => $params['pic'],
                'bank_branch' => $params['bank_branch'],
                'bank_account' => $params['bank_account'],
                'bank_holder' => $params['bank_holder']
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

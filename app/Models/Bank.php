<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Bank extends BaseModel
{
    use HasFactory;

    protected $table = 'cfmsbank';

    public $timestamps = false;

    function getAllData($params)
    {
        if ($params['fgactive'] == 'all') {
            $fgactive = "'Y','T'";
        } else {
            $fgactive = "'".$params['fgactive']."'";
        }
        // dd(var_dump($fgactive));
        $result = DB::select(
            "SELECT a.bankid as bank_id, a.bankname as bank_name,
            a.rekeningid as rekening_id, b.rekeningname as rekening_name,
            a.note, a.upduser, a.upddate, a.fgactive
            from cfmsbank a
            left join cfmsrekening b on a.rekeningid=b.rekeningid
            where a.fgactive in ($fgactive) and isnull(a.bankname, '') like :search_keyword
            order by a.bankname ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.bankid as bank_id, a.bankname as bank_name,
            a.rekeningid as rekening_id, b.rekeningname as rekening_name,
            a.note, a.upduser, a.upddate, a.fgactive
            from cfmsbank a
            left join cfmsrekening b on a.rekeningid=b.rekeningid
            where a.bankid = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($param)
    {
        $result = DB::insert(
            "INSERT INTO cfmsbank
            (bankid, bankname, rekeningid, note, upddate, upduser, fgactive) 
            VALUES 
            (:bankid, :bankname, :rekeningid, :note, getDate(), :upduser, 'Y')",
            [
                'bankid' => $param['bankid'],
                'bankname' => $param['bankname'],
                'rekeningid' => $param['rekeningid'],
                'note' => $param['note'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    function updateData($param)
    {
        $result = DB::update(
            "UPDATE cfmsbank SET 
            bankname = :bankname, rekeningid = :rekeningid, note = :note, 
            upddate = getDate(), upduser = :upduser, fgactive = :fgactive 
            WHERE bankid = :bankid",
            [
                'bankid' => $param['bankid'],
                'bankname' => $param['bankname'],
                'rekeningid' => $param['rekeningid'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'fgactive' => $param['fgactive']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            'DELETE FROM cfmsbank WHERE bankid = :bankid',
            [
                'bankid' => $id
            ]
        );

        return $result;
    }


    function cekData($id)
    {

        $result = DB::selectOne(
            'SELECT * from CFMsBank WHERE bankid = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

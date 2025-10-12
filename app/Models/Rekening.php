<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Rekening extends BaseModel
{
    use HasFactory;

    protected $table = 'cfmsrekening';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT a.rekeningid as rekening_id,a.rekeningname as rekening_name,
            a.grouprekid as group_rek_id,b.grouprekname as group_rek_name,
            a.upddate,a.upduser,a.note
            from cfmsrekening a
            inner join cfmsgrouprek b on a.grouprekid=b.grouprekid
            WHERE a.rekeningname LIKE :search_keyword 
            OR b.grouprekname LIKE :search_keyword2
            order by a.rekeningid",
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
            "SELECT a.rekeningid as rekening_id,a.rekeningname as rekening_name,
            a.grouprekid as group_rek_id,b.grouprekname as group_rek_name,
            a.upddate,a.upduser,a.note
            from cfmsrekening a
            inner join cfmsgrouprek b on a.grouprekid=b.grouprekid
            WHERE a.rekeningid = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO cfmsrekening (rekeningid, rekeningname, grouprekid,note,upddate,upduser)
            VALUES (:rekeningid, :rekeningname, :grouprekid,:note,getdate(),:upduser)",
            [
                'rekeningid' => $params['rekening_id'],
                'rekeningname' => $params['rekening_name'],
                'grouprekid' => $params['group_rek_id'],
                'note' => $params['note'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE cfmsrekening SET 
            rekeningname = :rekeningname,
            grouprekid = :grouprekid,
            note = :note,
            upddate = getdate(),
            upduser = :upduser
            WHERE rekeningid = :rekeningid",
            [
                'rekeningid' => $params['rekening_id'],
                'rekeningname' => $params['rekening_name'],
                'grouprekid' => $params['group_rek_id'],
                'note' => $params['note'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM cfmsrekening WHERE rekeningid = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from cfmsrekening WHERE rekeningid = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekTerpakai($id)
    {
        $result = DB::selectOne(
            'SELECT * from cftrkkbbdt WHERE rekeningid = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }


}

?>

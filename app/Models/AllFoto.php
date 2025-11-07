<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class AllFoto extends BaseModel
{
    use HasFactory;

    protected $table = 'allfoto';

    public $timestamps = false;

    function getDataById($id)
    {
        $result = DB::select(
            "SELECT a.voucherno as nota_beli,a.foto as foto, a.note as keterangan, a.fgtrans, a.upduser, a.upddate from allfoto a
            WHERE a.voucherno = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        //fgtrans 1 = pembelian, 2 = keuangan
        $result = DB::insert(
            "INSERT INTO allfoto (voucherno,foto,note,fgtrans,upduser,upddate)
            VALUES (:voucherid, :foto, :keterangan, :fgtrans, :upduser, getdate())",
            [
                'voucherid' => $params['id'],
                'foto' => $params['foto'],
                'keterangan' => $params['keterangan'],
                'fgtrans' => $params['fgtrans'],
                'upduser' => $params['upduser'],
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM allfoto WHERE voucherno = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

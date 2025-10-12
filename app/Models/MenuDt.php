<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class MenuDt extends BaseModel
{
    use HasFactory;

    protected $table = 'msmenudt';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msmenudt
            (kdmenu, kdbb, jumlah, kdsat, upddate, upduser)
            VALUES
            (:kdmenu, :kdbb, :jumlah, :kdsat, getdate(), :upduser)",
            [
                'kdmenu' => $params['menu_id'],
                'kdbb' => $params['bahan_baku_id'],
                'jumlah' => $params['qty'],
                'kdsat' => $params['satuan'],
                'upduser' => $params['upduser']
            ]
        );


        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msmenudt where kdmenu = :kdmenu",
            [
                'kdmenu' => $id
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::select(
            "SELECT
            a.kdmenu as menu_id,
            a.kdbb as bahan_baku_id,
            b.nmbb as bahan_baku_name,
            a.jumlah as qty,
            a.kdsat as satuan,
            a.upddate,
            a.upduser
            FROM msmenudt a
            LEFT JOIN msbahanbaku b ON a.kdbb = b.kdbb
            WHERE a.kdmenu = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

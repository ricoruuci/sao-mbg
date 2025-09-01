<?php

namespace App\Models\IN\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class INMsProduct extends Model
{
    use HasFactory;

    protected $table = 'inmsproduct';

    public $timestamps = false;

    public static $rulesInsert = [
        'productid' => 'required',
        'productdesc' => 'required',
    ];

    public static $messagesInsert = [
        'productid' => 'kode product harus diisi.',
        'productdesc' => 'Kolom nama product harus diisi !',
    ];

    public static $rulesUpdateAll = [
        'productid' => 'required',
        'productdesc' => 'required',
    ];

    public static $messagesUpdate = [
        'productid' => 'kode product tidak ditemukan.',
        'productdesc' => 'nama product harus diisi.',
    ];

    function getListData($param)
    {
        $result = DB::select(
            "SELECT productid,productdesc,upddate,upduser from inmsproduct where productid like :productidkeyword and 
            productdesc like :productdesckeyword order by productdesc",
            [
                'productidkeyword' => '%' . $param['productidkeyword'] . '%',
                'productdesckeyword' => '%' . $param['productdesckeyword'] . '%'
            ]

        );

        return $result;
    }

    function insertData($param)
    {
        $result = DB::insert(
            "INSERT inmsproduct (productid,productdesc,upddate,upduser)
            values (:productid,:productdesc,getdate(),:upduser)",
            [
                'productid' =>   $param['productid'],
                'productdesc' =>  $param['productdesc'],
                'upduser' =>  $param['upduser']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::insert(
            "UPDATE inmsproduct set
            productdesc = :productdesc,
            upddate = getdate(),
            upduser = :upduser
            where productid = :productid",
            [
                'productid' =>   $param['productid'],
                'productdesc' =>  $param['productdesc'],
                'upduser' =>  $param['upduser']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM inmsproduct WHERE productid = :productid',
            [
                'productid' => $param['productid']
            ]
        );

        return $result;
    }

    function cekProduct($productid)
    {

        $result = DB::selectOne(
            'SELECT * from inmsproduct WHERE productid = :productid',
            [
                'productid' => $productid
            ]
        );

        return $result;
    }
}

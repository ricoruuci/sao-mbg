<?php

namespace App\Models\AP\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class PurchaseOrderDt extends Model
{
    use HasFactory;

    protected $table = 'artrpenawarandt';

    public $timestamps = false;
    
    public static $rulesInsert = [
        'detail.*.itemid' => 'required',
        'detail.*.qty' => 'required',
        'detail.*.price' => 'required',
        'detail.*.itemname' => 'required'
    ];

    public static $messagesInsert = [
        'detail.*.itemid.required' => 'Kolom item ID (posisi ke-:position) harus diisi.',
        'detail.*.qty.required' => 'Kolom jumlah (posisi ke-:position) harus diisi.',
        'detail.*.price.required' => 'Kolom harga (posisi ke-:position) harus diisi.',
        'detail.*.itemname.required' => 'Kolom nama (posisi ke-:position) harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO artrpenawarandt            
            (gbuid,urut,itemid,produk,description,qty,price,upddate,upduser,partno) 
            VALUES 
            (:poid,:urut,:itemid,:itemname,:note,:qty,:price,getdate(),:upduser,:partno)", 
             
            [
                'poid' => $param['poid'],
                'urut' => $param['urut'],
                'itemid' => $param['itemid'],
                'itemname' => $param['itemname'],
                'note' => $param['note'],
                'qty' => $param['qty'],
                'price' => $param['price'],
                'upduser' => $param['upduser'],
                'partno' => $param['partno']
            ]
        );     

        return $result;
    }

    function getData($param)
    {
        $result = DB::select(
            "SELECT a.gbuid as poid,a.urut,a.itemid,a.produk as itemname,a.description as note,a.qty,a.price,a.upddate,a.upduser,a.partno,a.qty*a.price as total 
            from artrpenawarandt a WHERE a.gbuid = :poid ",
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }

    
    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM artrpenawarandt WHERE gbuid = :poid ',
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }
}

?>
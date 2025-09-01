<?php

namespace App\Models\AR\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class SalesOrderDt extends Model
{
    use HasFactory;

    protected $table = 'artrpurchaseorderdt';

    public $timestamps = false;

    public static $rulesInsert = [
        'detail.*.itemid' => 'required',
        'detail.*.qty' => 'required',
        'detail.*.price' => 'required',
        'detail.*.itemname' => 'required',
        'detail.*.modal' => 'required',
        'detail.*.bagasi' => 'required'
    ];

    public static $messagesInsert = [
        'detail.*.itemid.required' => 'Kolom item ID (posisi index ke-:index) harus diisi.',
        'detail.*.qty.required' => 'Kolom jumlah (posisi index ke-:index) harus diisi.',
        'detail.*.price.required' => 'Kolom harga (posisi index ke-:index) harus diisi.',
        'detail.*.itemname.required' => 'Kolom nama (posisi index ke-:index) harus diisi.',
        'detail.*.modal.required' => 'Kolom modal (posisi index ke-:index) harus diisi.',
        'detail.*.bagasi.required' => 'Kolom bagasi (posisi index ke-:index) harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO artrpurchaseorderdt
            (poid,itemid,qty,price,upddate,upduser,itemname,modal,bagasi,keterangan) 
            VALUES 
            (:poid,:itemid,:qty,:price,getdate(),:upduser,:itemname,:modal,:bagasi,:keterangan)",

            [
                'poid' => $param['poid'],
                'itemid' => $param['itemid'],
                'qty' => $param['qty'],
                'price' => $param['price'],
                'upduser' => $param['upduser'],
                'itemname' => $param['itemname'],
                'modal' => $param['modal'],
                'bagasi' => $param['bagasi'],
                'keterangan' => $param['keterangan']
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::select(
            "SELECT a.itemid,a.qty,a.price,b.uomid,a.upddate,a.upduser,a.itemname,a.modal,a.bagasi,isnull(a.keterangan,'') as keterangan,a.qty*(a.price+a.bagasi) as total
            from artrpurchaseorderdt a inner join inmsitem b on a.itemid=b.itemid WHERE a.poid = :poid ",
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }

    function getItemSOforPO($param)
    {
        $result = DB::select(
            "SELECT k.itemid,l.itemname,k.qty-k.jumpo as qty,k.price,l.uomid,k.keterangan,l.partno 
            from (
            select a.poid,isnull(a.modal,0) as price,isnull(a.qty,0) as qty,a.itemid,a.keterangan,isnull((select sum(x.qty) from artrpenawarandt x 
            inner join artrpenawaranhd y on x.gbuid=y.gbuid and y.flag='b' where y.soid=a.poid and x.itemid=a.itemid),0) as jumpo 
            from artrpurchaseorderdt a
            ) as k 
            inner join inmsitem l on k.itemid=l.itemid 
            where k.poid=:soid
            order by k.itemid ",
            [
                'soid' => $param['soid']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM artrpurchaseorderdt WHERE poid = :soid ',
            [
                'soid' => $param['soid']
            ]
        );

        return $result;
    }
}

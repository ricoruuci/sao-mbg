<?php

namespace App\Models\AR\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class PenjualanSN extends Model
{
    use HasFactory;

    protected $table = 'artrpenjualansn';

    public $timestamps = false;

    public static $rulesInsert = [
        'detailsn.*.itemid' => 'required'
    ];

    public static $messagesInsert = [
        'detailsn.*.itemid.required' => 'Kolom kode mobil (posisi index ke-:index) harus diisi.'
    ];

    public static $rulesInsert2 = [
        'itemid' => 'required'
    ];

    public static $messagesInsert2 = [
        'itemid' => 'Kolom kode mobil harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO ARTrPenjualansn
            (snid,saleid,itemid,price,warehouseid,upddate,upduser,modal,purchaseid,fgsn)
            VALUES
            (:snid,:saleid,:itemid,:price,:warehouseid,getdate(),:upduser,:modal,:purchaseid,:fgsn)",

            [
                'snid' => $param['snid'],
                'saleid' => $param['saleid'],
                'itemid' => $param['itemid'],
                'price' => $param['price'],
                'warehouseid' => '01GU',
                'upduser' => $param['upduser'],
                'modal' => $param['modal'],
                'purchaseid' => $param['purchaseid'],
                'fgsn' => $param['fgsn']
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::select(
            'SELECT a.snid,a.saleid,a.itemid,b.itemname,a.price,a.modal,a.purchaseid,a.upddate,a.upduser,a.fgsn from ARTrPenjualansn a
            inner join inmsitem b on a.itemid=b.itemid
            WHERE a.saleid = :saleid and a.itemid=:itemid ',
            [
                'saleid' => $param['saleid'],
                'itemid' => $param['itemid']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM ARTrPenjualanSN WHERE saleid = :saleid and itemid = :itemid ',
            [
                'saleid' => $param['saleid'],
                'itemid' => $param['itemid']
            ]
        );

        return $result;
    }

    function selectSN($param)
    {
        $result = DB::select(
            "SELECT k.itemid,k.itemname,k.snid,k.purchaseid,k.transdate,k.suppid, k.suppname,k.purchaseid,k.price as modal,k.fgsn as fgsn
            from (
            select a.snid, c.konsinyasiid as purchaseid, c.transdate, c.suppid, d.suppname, b.itemid, f.itemname, a.fgjual, b.price,a.fgsn
            from APTrPurchaseDtSN a
            inner join aptrpurchasedt b on a.PurchaseID=b.PurchaseID and a.itemid=b.itemid
            inner join aptrpurchasehd c on b.PurchaseID=c.PurchaseID
            inner join apmssupplier d on c.suppid=d.suppid
            inner join inmsitem f on f.itemid=b.itemid
            ) as k
			where k.itemid= :itemid and k.fgjual='T'
            and isnull(k.suppid,'') like :suppidkeyword
            and isnull(k.suppname,'') like :suppnamekeyword
            and isnull(k.purchaseid,'') like :purchaseidkeyword
            and isnull(k.snid,'') like :snidkeyword
            order by k.snid ",
            [
                'itemid' => $param['itemid'],
                'suppidkeyword' => '%' . $param['suppidkeyword'] . '%',
                'suppnamekeyword' => '%' . $param['suppnamekeyword'] . '%',
                'purchaseidkeyword' => '%' . $param['purchaseidkeyword'] . '%',
                'snidkeyword' => '%' . $param['snidkeyword'] . '%'
            ]
        );

        return $result;
    }
}

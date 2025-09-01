<?php

namespace App\Models\AP\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class PembelianDt extends Model
{
    use HasFactory;

    protected $table = 'aptrpurchasedt';

    public $timestamps = false;

    public static $rulesInsert = [
        'detail.*.itemid' => 'required',
        'detail.*.qty' => 'required',
        'detail.*.price' => 'required'
    ];

    public static $messagesInsert = [
        'detail.*.itemid.required' => 'Kolom kode mobil (posisi index ke-:position) harus diisi.',
        'detail.*.qty.required' => 'Kolom jumlah (posisi index ke-:position) harus diisi.',
        'detail.*.price.required' => 'Kolom harga (posisi index ke-:position) harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO aptrpurchasedt
            (purchaseid,itemid,suppid,qty,warehouseid,price,disc,upddate,upduser)
            VALUES
            (:purchaseid,:itemid,:suppid,:qty,'01GU',:price,0,getdate(),:upduser)",
            [
                'purchaseid' => $param['purchaseid'],
                'suppid' => $param['suppid'],
                'itemid' => $param['itemid'],
                'qty' => $param['qty'],
                'price' => $param['price'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $detailsn = new PembelianDtSN();

        $result = DB::select(
            "SELECT a.itemid,b.itemname,isnull(a.qty,0) as qty,isnull(a.price,0) as price,isnull(a.price*a.qty,0) as total from aptrpurchasedt a
            inner join inmsitem b on a.itemid=b.itemid  WHERE a.purchaseid = :purchaseid ",
            [
                'purchaseid' => $param['purchaseid']
            ]
        );

        foreach ($result as $data) {

            $datadetailResult = $detailsn->getData([
                'purchaseid' => $param['purchaseid'],
                'itemid' => $data->itemid,
            ]);

            $data->detailsn = $datadetailResult;
        }

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM aptrpurchasedt WHERE purchaseid = :purchaseid ',
            [
                'purchaseid' => $param['purchaseid']
            ]
        );

        return $result;
    }
}

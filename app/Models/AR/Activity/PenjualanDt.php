<?php

namespace App\Models\AR\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class PenjualanDt extends Model
{
    use HasFactory;

    protected $table = 'artrpenjualandt';

    public $timestamps = false;

    public static $rulesInsert = [
        'detail.*.itemid' => 'required',
        'detail.*.qty' => 'required',
        'detail.*.price' => 'required'
    ];

    public static $messagesInsert = [
        'detail.*.itemid.required' => 'Kolom kode mobil (posisi index ke-:index) harus diisi.',
        'detail.*.qty.required' => 'Kolom jumlah (posisi index ke-:index) harus diisi.',
        'detail.*.price.required' => 'Kolom harga (posisi index ke-:index) harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO artrpenjualandt
            (saleid,itemid,qty,price,note,uomid,warehouseid,komisi,upduser,upddate)
            VALUES
            (:saleid,:itemid,:qty,:price,:note,'Unit','01GU',0,:upduser,getdate())",

            [
                'saleid' => $param['saleid'],
                'itemid' => $param['itemid'],
                'qty' => $param['qty'],
                'price' => $param['price'],
                'upduser' => $param['upduser'],
                'note' => $param['note']
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $detailsn = new PenjualanSN();

        $result = DB::select(
            "SELECT a.saleid,a.itemid,b.itemname,isnull(a.qty,0) as qty,a.uomid,isnull(a.price,0) as price,isnull(a.qty*a.price,0) as total,isnull(a.note,'') as note,
            a.upduser,a.upddate
            from ARTrPenjualandt a
            inner join inmsitem b on a.itemid=b.itemid WHERE a.saleid = :saleid ",
            [
                'saleid' => $param['saleid']
            ]
        );

        foreach ($result as $data) {

            $datadetailResult = $detailsn->getData([
                'saleid' => $param['saleid'],
                'itemid' => $data->itemid
            ]);

            $data->detailsn = $datadetailResult;
        }

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM artrpenjualandt WHERE saleid = :saleid ',
            [
                'saleid' => $param['saleid']
            ]
        );

        return $result;
    }
}

<?php

namespace App\Models\AR\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class PenjualanHd extends BaseModel
{
    use HasFactory;

    protected $table = 'artrpenjualanhd';

    public $timestamps = false;

    public static $rulesInsert = [
        'custid' => 'required',
        'transdate' => 'required',
        'salesid' => 'required'
    ];

    public static $messagesInsert = [
        'custid' => 'Kolom kode pelanggan harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.',
        'salesid' => 'Kolom kode sales harus diisi.'
    ];

    public static $rulesUpdateAll = [
        'saleid' => 'required',
        'custid' => 'required',
        'transdate' => 'required',
        'salesid' => 'required'
    ];

    public static $messagesUpdate = [
        'saleid' => 'nomor penjualan tidak ditemukan.',
        'custid' => 'Kolom kode pelanggan harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.',
        'salesid' => 'Kolom kode sales harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO artrpenjualanhd (saleid,poid,transdate,custid,salesid,note,jatuhtempo,discount,dp,flagcounter,rate,charge,fglunas,administrasi,
            taxid,fgtax,ppn,ppnfee,fgform,fgtrans,currid,upduser,upddate)
            VALUES (:saleid,:poid,:transdate,:custid,:salesid,:note,0,0,0,'L',0,0,'S',0,
            :fpsid,:fgtax,0,:nilaitax,'AR','T','IDR',:upduser,getdate())",
            [
                'saleid' => $param['saleid'],
                'poid' => $param['poid'],
                'custid' => $param['custid'],
                'nilaitax' => $param['nilaitax'],
                'fgtax' => $param['fgtax'],
                'transdate' => $param['transdate'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'salesid' => $param['salesid'],
                'fpsid' => $param['fpsid']
            ]
        );

        return $result;
    }

    function getListData($param)
    {
        if ($param['sortby'] == 'new') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }
        $result = DB::select(
            "SELECT a.saleid,a.poid,a.transdate,a.custid,b.custname,a.salesid,c.salesname,a.taxid as fpsid, isnull(a.note,'') as note,
            a.fgtax,a.ppnfee as nilaitax,isnull(a.stpj,0) as subtotal,isnull(a.ppn,0) as ppn,isnull(a.ttlpj,0) as grandtotal
            from artrpenjualanhd a
            inner join armscustomer b on a.custid=b.custid
            inner join armssales c on a.salesid=c.salesid
            where convert(varchar(10),a.transdate,112) between :dari and :sampai
            and isnull(a.saleid,'') like :saleidkeyword
            and isnull(a.custid,'') like :custidkeyword  and isnull(a.poid,'') like :poidkeyword
            and isnull(b.custname,'') like :custnamekeyword and isnull(a.salesid,'') like :salesidkeyword
            and isnull(c.salesname,'') like :salesnamekeyword
            order by a.transdate $order",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'saleidkeyword' => '%' . $param['saleidkeyword'] . '%',
                'custidkeyword' => '%' . $param['custidkeyword'] . '%',
                'poidkeyword' => '%' . $param['poidkeyword'] . '%',
                'custnamekeyword' => '%' . $param['custnamekeyword'] . '%',
                'salesidkeyword' => '%' . $param['salesidkeyword'] . '%',
                'salesnamekeyword' => '%' . $param['salesnamekeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT a.saleid,a.poid,a.transdate,a.custid,b.custname,a.salesid,c.salesname,a.taxid as fpsid, isnull(a.note,'') as note,
            a.fgtax,a.ppnfee as nilaitax,isnull(a.stpj,0) as subtotal,isnull(a.ppn,0) as ppn,isnull(a.ttlpj,0) as grandtotal
            from artrpenjualanhd a
            inner join armscustomer b on a.custid=b.custid
            inner join armssales c on a.salesid=c.salesid
            where a.saleid = :saleid",
            [
                'saleid' => $param['saleid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE artrpenjualanhd SET 
            poid = :poid,
            custid = :custid,
            transdate = :transdate,
            note = :note,
            upddate = getdate(),
            upduser = :upduser,
            salesid = :salesid,
            ppnfee = :nilaitax,
            taxid = :fpsid,
            fgtax = :fgtax
            WHERE saleid = :saleid',
            [
                'saleid' => $param['saleid'],
                'poid' => $param['poid'],
                'custid' => $param['custid'],
                'transdate' => $param['transdate'],
                'note' => $param['note'],
                'fpsid' => $param['fpsid'],
                'upduser' => $param['upduser'],
                'nilaitax' => $param['nilaitax'],
                'salesid' => $param['salesid'],
                'fgtax' => $param['fgtax']
            ]
        );

        return $result;
    }

    function deleteAllItem($param)
    {
        $result = DB::delete(
            'DELETE from allitem where voucherno = :saleid ',
            [
                'saleid' => $param['saleid']
            ]
        );

        return $result;
    }

    function insertAllItem($param)
    {
        $result = DB::insert(
            "INSERT allitem (VoucherNo,TransDate,WareHouseId,ItemID,FgTrans,Qty,Price,ModuleID,TempField2,currid) values
            (:saleid,:transdate,:warehouseid,:itemid,55,:qty,:price,'AR',:custname,'IDR') ",
            [
                'saleid' => $param['saleid'],
                'transdate' => $param['transdate'],
                'warehouseid' => '01GU',
                'itemid' => $param['itemid'],
                'qty' => $param['qty'],
                'price' => $param['price'],
                'custname' => $param['custname']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM artrpenjualanhd WHERE saleid = :saleid',
            [
                'saleid' => $param['saleid']
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($transdate)
    {

        $year = substr($transdate, 2, 4);

        $autoNumber = $this->autoNumber($this->table, 'saleid', 'SALE-SM/' . $year . '/', '0000');

        return $autoNumber;
    }

    function hitungTotal($param)
    {

        $result = DB::selectONe(
            "SELECT K.saleid,k.subtotal,k.ppn,k.subtotal+k.ppn as grandtotal FROM (
            SELECT b.saleid, ISNULL(SUM(A.Qty*A.Price),0) as subtotal,
            case when b.FgTax = 'Y' then ISNULL((SUM(A.Qty*A.Price) * b.ppnfee * 0.01),0) else 0 end as ppn
            FROM ARTrPenjualanDt A
            inner join ARTrPenjualanHd b on a.saleid=b.saleid
            group by b.ppnfee,b.FgTax,b.saleid
            ) as K
            WHERE k.SaleID = :saleid",
            [
                'saleid' => $param['saleid']
            ]
        );

        return $result;
    }

    function updateTotal($param)
    {
        $result = DB::update(
            'UPDATE artrpenjualanhd SET ttlpj = :grandtotal, stpj = :subtotal, ppn = :ppn WHERE saleid = :saleid',
            [
                'saleid' => $param['saleid'],
                'grandtotal' => $param['grandtotal'],
                'subtotal' => $param['subtotal'],
                'ppn' => $param['ppn']
            ]
        );

        return $result;
    }

    function cekPenjualan($saleid)
    {
        $result = DB::selectOne(
            'SELECT * from artrpenjualanhd WHERE saleid = :saleid',
            [
                'saleid' => $saleid
            ]
        );

        return $result;
    }
}

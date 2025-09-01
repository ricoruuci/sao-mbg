<?php

namespace App\Models\AP\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class PembelianHd extends BaseModel
{
    use HasFactory;

    protected $table = 'aptrpurchasehd';

    public $timestamps = false;

    public static $rulesInsert = [
        'transdate' => 'required',
        'suppid' => 'required'
    ];

    public static $messagesInsert = [
        'suppid' => 'Kolom kode supplier harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.'
    ];

    public static $rulesUpdateAll = [
        'purchaseid' => 'required',
        'transdate' => 'required'
    ];

    public static $messagesUpdate = [
        'purchaseid' => 'nota pembelian tidak ditemukan.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO aptrpurchasehd
        (purchaseid,suppid,transdate,note,currid,rate,jatuhtempo,fgtax,nilaitax,fpsid,fgform,fgoto,upddate,upduser)
            VALUES
        (:purchaseid,:suppid,:transdate,:note,'IDR',0,0,:fgtax,:nilaitax,:fpsid,'AP','T',getdate(),:upduser)",
            [
                'purchaseid' => $param['purchaseid'],
                'transdate' => $param['transdate'],
                'fpsid' => $param['fpsid'],
                'suppid' => $param['suppid'],
                'nilaitax' => $param['nilaitax'],
                'fgtax' => $param['fgtax'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE aptrpurchasehd SET
            transdate = :transdate,
            suppid = :suppid,
            fgtax = :fgtax,
            nilaitax = :nilaitax,
            fpsid = :fpsid,
            note = :note,
            upddate = getdate(),
            upduser = :upduser
            WHERE purchaseid = :purchaseid',
            [
                'purchaseid' => $param['purchaseid'],
                'transdate' => $param['transdate'],
                'suppid' => $param['suppid'],
                'fpsid' => $param['fpsid'],
                'fgtax' => $param['fgtax'],
                'nilaitax' => $param['nilaitax'],
                'note' => $param['note'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    function getListData($param)
    {
        if ($param['sortby'] == 'id') {
            $order = 'a.purchaseid ';
        } else
        if ($param['sortby'] == 'datenew') {
            $order = 'a.transdate DESC ';
        } else if ($param['sortby'] == 'dateold') {
            $order = 'a.transdate ';
        }

        $result = DB::select(
            "SELECT a.purchaseid,a.suppid,b.suppname,a.transdate,isnull(a.note,'') as note,a.fgtax,isnull(a.nilaitax,11) as nilaitax,a.fpsid,a.fgoto,
            case when a.fgoto = 'T' then 'Belum Otorisasi' else 'Sudah Otorisasi' end as statusoto,a.upduser,a.upddate
            from aptrpurchasehd a
            inner join apmssupplier b on a.suppid=b.suppid
            WHERE convert(varchar(10),a.transdate,112) between :dari and :sampai and
            isnull(a.suppid,'') like :suppidkeyword and isnull(b.suppname,'') like :suppnamekeyword and
            isnull(a.purchaseid,'') like :purchaseidkeyword
            order by $order ",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'purchaseidkeyword' => '%' . $param['purchaseidkeyword'] . '%',
                'suppidkeyword' => '%' . $param['suppidkeyword'] . '%',
                'suppnamekeyword' => '%' . $param['suppnamekeyword'] . '%',
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT a.purchaseid,a.suppid,b.suppname,a.transdate,isnull(a.note,'') as note,a.fgtax,isnull(a.nilaitax,11) as nilaitax,a.fpsid,a.fgoto,
            case when a.fgoto = 'T' then 'Belum Otorisasi' else 'Sudah Otorisasi' end as statusoto,a.upduser,a.upddate
            from aptrpurchasehd a
            inner join apmssupplier b on a.suppid=b.suppid
            WHERE a.purchaseid = :purchaseid ",
            [
                'purchaseid' => $param['purchaseid']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM aptrpurchasehd WHERE purchaseid = :purchaseid',
            [
                'purchaseid' => $param['purchaseid']
            ]
        );

        return $result;
    }

    function deleteAllItem($param)
    {
        $result = DB::delete(
            'DELETE from allitem where voucherno = :purchaseid ',
            [
                'purchaseid' => $param['purchaseid']
            ]
        );

        return $result;
    }

    function insertAllItem($param)
    {
        $result = DB::insert(
            "INSERT allitem (VoucherNo,TransDate,WareHouseId,ItemID,FgTrans,Qty,Price,ModuleID,TempField2,currid) values
            (:purchaseid,:transdate,:warehouseid,:itemid,7,:qty,:price,'AP',:suppname,'IDR') ",
            [
                'purchaseid' => $param['purchaseid'],
                'transdate' => $param['transdate'],
                'warehouseid' => '01GU',
                'itemid' => $param['itemid'],
                'qty' => $param['qty'],
                'price' => $param['price'],
                'suppname' => $param['suppname']
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($transdate)
    {
        $pt = 'AP-SM';

        $year = substr($transdate, 2, 4);

        // $month = substr($transdate, 4, 2);

        $autoNumber = $this->autoNumber($this->table, 'purchaseid', $pt . '/' . $year .  '/', '0000');

        return $autoNumber;
    }

    function cekInvoice($purchaseid)
    {

        $result = DB::selectOne(
            'SELECT * from aptrpurchasehd WHERE purchaseid = :purchaseid',
            [
                'purchaseid' => $purchaseid
            ]
        );

        return $result;
    }

    function hitungTotal($param)
    {

        $result = DB::selectONe(
            "SELECT k.purchaseid,k.subtotal,k.ppn,k.subtotal+k.ppn as grandtotal from (
            select b.purchaseid, isnull(sum(a.qty*a.price),0) as subtotal,
            case when b.fgtax = 'Y' then isnull((sum(a.qty*a.price) * b.nilaitax * 0.01),0) else 0 end as ppn
            from aptrpurchasedt a
            inner join aptrpurchasehd b on a.purchaseid=b.purchaseid
            group by b.nilaitax,b.fgtax,b.purchaseid
            ) as k
            where k.purchaseid= :purchaseid",
            [
                'purchaseid' => $param['purchaseid']
            ]
        );

        return $result;
    }

    function updateTotal($param)
    {
        $result = DB::update(
            'UPDATE aptrpurchasehd SET ttlpb = :grandtotal, stpb = :subtotal, ppn = :ppn  WHERE purchaseid = :purchaseid ',
            [
                'purchaseid' => $param['purchaseid'],
                'grandtotal' => $param['grandtotal'],
                'subtotal' => $param['subtotal'],
                'ppn' => $param['ppn']
            ]
        );
        return $result;
    }
}

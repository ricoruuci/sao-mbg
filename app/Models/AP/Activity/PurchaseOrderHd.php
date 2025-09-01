<?php

namespace App\Models\AP\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class PurchaseOrderHd extends BaseModel
{
    use HasFactory;

    protected $table = 'artrpenawaranhd';

    public $timestamps = false;

    public static $rulesInsert = [
        'suppid' => 'required',
        'transdate' => 'required',
        'purchasingid' => 'required',
        'suppname' => 'required'
    ];

    public static $messagesInsert = [
        'suppid' => 'Kolom kode supplier harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.',
        'purchasingid' => 'Kolom kode purchasing harus diisi.',
        'suppname' => 'Kolom nama supplier harus diisi.'
    ];

    public static $rulesUpdateAll = [
        'poid' => 'required',
        'suppid' => 'required',
        'transdate' => 'required',
        'purchasingid' => 'required',
        'suppname' => 'required'
    ];

    public static $messagesUpdate = [
        'poid' => 'nomor purchase order tidak ditemukan.',
        'suppid' => 'Kolom kode supplier harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.',
        'purchasingid' => 'Kolom kode purchasing harus diisi.',
        'suppname' => 'Kolom nama supplier harus diisi.'
    ];

    public function insertData($param)
    {
        $result = DB::insert(
            "INSERT INTO artrpenawaranhd 
            (gbuid,transdate,currid,customer,up,salesid,phone,fax,email,note,upddate,upduser,ttlgbu,fgtax,disc,custid,flag,fob,soid,ppn) 
            VALUES 
            (:poid,:transdate,:currid,:suppname,:up,:purchasingid,:telp,:fax,:email,:note,getdate(),:upduser,0,:fgtax,:discamount,:suppid,'B','Jakarta',:soid,:nilaitax)",
            [
                'poid' => $param['poid'],
                'transdate' => $param['transdate'],
                'currid' => $param['currid'],
                'suppname' => $param['suppname'],
                'up' => $param['up'],
                'purchasingid' => $param['purchasingid'],
                'telp' => $param['telp'],
                'fax' => $param['fax'],
                'email' => $param['email'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'fgtax' => $param['fgtax'],
                'discamount' => $param['discamount'],
                'suppid' => $param['suppid'],
                'soid' => $param['soid'],
                'nilaitax' => $param['nilaitax']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            "UPDATE artrpenawaranhd SET 
            transdate = :transdate,
            currid = :currid,
            customer = :suppname,
            up = :up,
            salesid = :purchasingid,
            phone = :telp,
            fax = :fax,
            email = :email,
            note = :note,
            upddate = getdate(),
            upduser = :upduser,
            fgtax = :fgtax,
            custid = :suppid,
            disc = :discamount,
            ppn = :nilaitax
            WHERE gbuid = :poid and flag='B' ",
            [
                'poid' => $param['poid'],
                'transdate' => $param['transdate'],
                'currid' => $param['currid'],
                'suppname' => $param['suppname'],
                'up' => $param['up'],
                'purchasingid' => $param['purchasingid'],
                'telp' => $param['telp'],
                'fax' => $param['fax'],
                'email' => $param['email'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'fgtax' => $param['fgtax'],
                'discamount' => $param['discamount'],
                'suppid' => $param['suppid'],
                'nilaitax' => $param['nilaitax']
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
            "SELECT a.gbuid as poid,a.transdate,a.currid,a.custid as suppid,a.customer as suppname,a.up,a.phone as telp,a.fax,a.email,
            a.note, a.salesid as purchasingid,b.salesname as purchasingname,a.upddate,a.upduser,a.soid,d.custname,
            case when a.fgtax='y' then a.ttlgbu/(1+(a.ppn*0.01))+disc else a.ttlgbu+disc end as subtotal,
            case when a.fgtax='y' then a.ttlgbu/(1+(a.ppn*0.01))*(a.ppn*0.01) else 0 end as ppnamount,a.fgtax,a.ppn as nilaitax,
            a.disc as discamount,a.ttlgbu as total
            from artrpenawaranhd a 
            inner join armssales b on a.salesid=b.salesid
            left join artrpurchaseorderhd c on a.soid=c.poid
            left join armscustomer d on c.custid=d.custid
            where a.flag='b' and convert(varchar(10),a.transdate,112) between :dari and :sampai 
            and isnull(a.custid,'') like :suppidkeyword  and a.gbuid like :poidkeyword  
            and isnull(a.salesid,'') like :purchasingidkeyword and isnull(a.customer,'') like :suppnamekeyword  
            and isnull(b.salesname,'') like :purchasingnamekeyword and isnull(a.soid,'') like :soidkeyword
            order by a.transdate $order",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'suppidkeyword' => '%' . $param['suppidkeyword'] . '%',
                'suppnamekeyword' => '%' . $param['suppnamekeyword'] . '%',
                'poidkeyword' => '%' . $param['poidkeyword'] . '%',
                'purchasingidkeyword' => '%' . $param['purchasingidkeyword'] . '%',
                'purchasingnamekeyword' => '%' . $param['purchasingnamekeyword'] . '%',
                'soidkeyword' => '%' . $param['soidkeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT a.gbuid as poid,a.transdate,a.currid,a.custid as suppid,a.customer as suppname,a.up,a.phone as telp,a.fax,a.email,
            a.note, a.salesid as purchasingid,b.salesname as purchasingname,a.upddate,a.upduser,a.soid,d.custname,
            case when a.fgtax='y' then a.ttlgbu/(1+(a.ppn*0.01))+disc else a.ttlgbu+disc end as subtotal,
            case when a.fgtax='y' then a.ttlgbu/(1+(a.ppn*0.01))*(a.ppn*0.01) else 0 end as ppnamount,a.fgtax,a.ppn as nilaitax,
            a.disc as discamount,a.ttlgbu as total
            from artrpenawaranhd a 
            inner join armssales b on a.salesid=b.salesid
            left join artrpurchaseorderhd c on a.soid=c.poid
            left join armscustomer d on c.custid=d.custid
            WHERE a.gbuid = :poid",
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            "DELETE FROM artrpenawaranhd WHERE gbuid = :poid and flag='B' ",
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($transdate)
    {

        $year = substr($transdate, 0, 4);
        $month = substr($transdate, 4, 2);

        $autoNumber = $this->autoNumber($this->table, 'gbuid', 'CSI-PO/' . $year . '-' . $month, '0000');

        return $autoNumber;
    }

    function hitungTotal($param)
    {

        $result = DB::selectONe(
            "SELECT isnull(case when k.fgtax='t' then k.subtotal-k.disc else (k.subtotal-k.disc)+(k.subtotal-k.disc)*0.01*k.ppn end,0) as grandtotal 
            from (
            select isnull(sum(a.qty*a.price),0) as subtotal,b.fgtax,b.gbuid,b.disc,isnull(b.ppn,10) as ppn
            from artrpenawarandt a inner join artrpenawaranhd b on a.gbuid=b.gbuid
            group by b.fgtax,b.gbuid,b.disc,b.ppn) as k
            where k.gbuid=:poid ",
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }

    function updateTotal($param)
    {
        $result = DB::update(
            "UPDATE artrpenawaranhd SET ttlgbu = :grandtotal WHERE gbuid = :poid and flag='B' ",
            [
                'poid' => $param['poid'],
                'grandtotal' => $param['grandtotal']
            ]
        );

        return $result;
    }

    function cekSalesorder($soid)
    {
        $result = DB::selectOne(
            "SELECT * from artrpenawaranhd WHERE gbuid = :poid and flag='B' ",
            [
                'poid' => $soid
            ]
        );

        return $result;
    }
}

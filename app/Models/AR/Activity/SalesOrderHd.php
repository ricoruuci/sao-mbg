<?php

namespace App\Models\AR\Activity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class SalesOrderHd extends BaseModel
{
    use HasFactory;

    protected $table = 'artrpurchaseorderhd';

    public $timestamps = false;

    public static $rulesInsert = [
        'custid' => 'required',
        'transdate' => 'required',
        'tglkirim' => 'required',
        'salesid' => 'required'
    ];

    public static $messagesInsert = [
        'custid' => 'Kolom kode pelanggan harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.',
        'tglkirim' => 'Kolom tanggal kirim harus diisi.',
        'salesid' => 'Kolom kode sales harus diisi.'
    ];

    public static $rulesUpdateAll = [
        'soid' => 'required',
        'custid' => 'required',
        'transdate' => 'required',
        'tglkirim' => 'required',
        'salesid' => 'required'
    ];

    public static $messagesUpdate = [
        'soid' => 'nomor so tidak ditemukan.',
        'custid' => 'Kolom kode pelanggan harus diisi.',
        'transdate' => 'Kolom tanggal transaksi harus diisi.',
        'tglkirim' => 'Kolom tanggal kirim harus diisi.',
        'salesid' => 'Kolom kode sales harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO artrpurchaseorderhd (poid,prid,custid,transdate,note,upddate,upduser,tglkirim,fgcetak,salesid,jenis,currid,fob,ppn,fgclose) 
            VALUES (:poid,:prid,:custid,:transdate,:note,getdate(),:upduser,:tglkirim,'Y',:salesid,'T',:currid,:fob,:ppn,'T')",
            [
                'poid' => $param['poid'],
                'prid' => $param['prid'],
                'custid' => $param['custid'],
                'transdate' => $param['transdate'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'tglkirim' => $param['tglkirim'],
                'salesid' => $param['salesid'],
                'currid' => $param['currid'],
                'fob' => $param['fob'],
                'ppn' => $param['ppn']
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
            "SELECT a.poid,a.currid,a.transdate,a.tglkirim,a.fob,a.custid,b.custname,a.salesid,c.salesname,a.prid,a.jenis,a.ppn,a.ttlso,
			case when a.ppn<>0 then a.ttlso/(1+(a.ppn/100)) else a.ttlso end as stso,
			case when a.ppn<>0 then a.ttlso/(1+(a.ppn/100))*(a.ppn/100) else 0 end as ppnamount,a.upduser,a.upddate,
            isnull((select top 1 x.saleid from artrpenjualanhd x where isnull(x.soid,'')=a.poid),'') as invoice,
            isnull((select sum(x.qty*(x.price-x.modal)) from artrpurchaseorderdt x where x.poid=a.poid),0) as margin,
            a.otoby,a.otodate,a.fgclose,a.closeby,a.closedate
            from artrpurchaseorderhd a
            inner join armscustomer b on a.custid=b.custid
            inner join armssales c on a.salesid=c.salesid
            where convert(varchar(10),a.transdate,112) between :dari and :sampai 
            and isnull(a.custid,'') like :custidkeyword  and a.poid like :soidkeyword 
            and isnull(b.custname,'') like :custnamekeyword and isnull(a.salesid,'') like :salesidkeyword
            and isnull(c.salesname,'') like :salesnamekeyword
            order by a.transdate $order",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'custidkeyword' => '%' . $param['custidkeyword'] . '%',
                'soidkeyword' => '%' . $param['soidkeyword'] . '%',
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
            "SELECT a.poid,a.currid,a.transdate,a.tglkirim,a.fob,a.custid,b.custname,a.salesid,c.salesname,a.prid,a.jenis,a.ppn,a.ttlso,
			case when a.ppn<>0 then a.ttlso/(1+(a.ppn/100)) else a.ttlso end as stso,
			case when a.ppn<>0 then a.ttlso/(1+(a.ppn/100))*(a.ppn/100) else 0 end as ppnamount,a.upduser,a.upddate,
            isnull((select top 1 x.saleid from artrpenjualanhd x where isnull(x.soid,'')=a.poid),'') as invoice,
            isnull((select sum(x.qty*(x.price-x.modal)) from artrpurchaseorderdt x where x.poid=a.poid),0) as margin,
            a.otoby,a.otodate,a.fgclose,a.closeby,a.closedate
            from artrpurchaseorderhd a 
            inner join armscustomer b on a.custid=b.custid
            inner join armssales c on a.salesid=c.salesid
            WHERE a.poid = :poid",
            [
                'poid' => $param['poid']
            ]
        );

        return $result;
    }

    function getListOto()
    {
        $result = DB::select(
            "SELECT a.poid,a.currid,a.transdate,a.tglkirim,a.fob,a.custid,b.custname,a.salesid,c.salesname,a.prid,a.jenis,a.ppn,a.ttlso,
			case when a.ppn<>0 then a.ttlso/(1+(a.ppn/100)) else a.ttlso end as stso,
			case when a.ppn<>0 then a.ttlso/(1+(a.ppn/100))*(a.ppn/100) else 0 end as ppnamount,a.upduser,a.upddate,
            isnull((select top 1 x.saleid from artrpenjualanhd x where isnull(x.soid,'')=a.poid),'') as invoice,
            isnull((select sum(x.qty*(x.price-x.modal)) from artrpurchaseorderdt x where x.poid=a.poid),0) as margin,
            a.otoby,a.otodate,a.fgclose,a.closeby,a.closedate
            from artrpurchaseorderhd a 
            inner join armscustomer b on a.custid=b.custid
            inner join armssales c on a.salesid=c.salesid
            WHERE a.jenis not in ('X','Y') "
        );

        return $result;
    }

    function getSOforPO($param)
    {
        $result = DB::select(
            "SELECT k.poid as soid,k.transdate,k.custname,k.jumlah as ttlso from (
            select a.poid,a.transdate,sum(b.qty) as total,a.jenis,c.custname,isnull(a.ttlso,0) as jumlah,
            isnull((select sum(x.qty) from artrpenawarandt x inner join artrpenawaranhd y on x.gbuid=y.gbuid and y.flag='b' 
            where y.soid=a.poid),0) as jumpo 
            from artrpurchaseorderhd a 
            inner join artrpurchaseorderdt b on a.poid=b.poid 
            inner join armscustomer c on a.custid=c.custid 
            where isnull(a.fgclose,'T')='T'
            group by a.poid,a.transdate,a.jenis,c.custname,a.ttlso
            ) as k where k.total-k.jumpo > 0 and k.jenis='Y' 
            and convert(varchar(8),k.transdate,112) <= :transdate and k.poid like :soidkeyword and k.custname like :custnamekeyword
            order by k.poid ",
            [
                "transdate" => $param['transdate'],
                'soidkeyword' => '%' . $param['soidkeyword'] . '%',
                'custnamekeyword' => '%' . $param['custnamekeyword'] . '%'
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE artrpurchaseorderhd SET 
            prid = :prid,
            custid = :custid,
            transdate = :transdate,
            note = :note,
            upddate = getdate(),
            upduser = :upduser,
            tglkirim = :tglkirim,
            salesid = :salesid,
            currid = :currid,
            fob = :fob,
            ppn = :ppn
            WHERE poid = :soid',
            [
                'soid' => $param['soid'],
                'prid' => $param['prid'],
                'custid' => $param['custid'],
                'transdate' => $param['transdate'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'tglkirim' => $param['tglkirim'],
                'salesid' => $param['salesid'],
                'currid' => $param['currid'],
                'fob' => $param['fob'],
                'ppn' => $param['ppn']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM artrpurchaseorderhd WHERE poid = :soid',
            [
                'soid' => $param['soid']
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($transdate)
    {

        $year = substr($transdate, 2, 4);

        $autoNumber = $this->autoNumber($this->table, 'poid', 'SO-CSI/' . $year . '/', '0000');

        return $autoNumber;
    }

    function cekOtorisasi($param)
    {

        $result = DB::selectOne(
            "SELECT (case when x.sisalimit-x.nilaisobaru<0 then 'L' else (case when x.overdue>0 then 'D' else 'Y' end) end) as flag
            from (
                select k.custid,isnull(l.limitpiutang-isnull(sum(k.total-k.bayar),0),0) as sisalimit,
                isnull((select m.ttlso from artrpurchaseorderhd m where m.poid=:soid ),0) as nilaisobaru,
                isnull(sum(case when convert(varchar(8),dateadd(day,k.term,k.transdate),112) >= :transdate then 1 else 0 end),0) as overdue
                    from (
                    select a.transdate,isnull(b.term,0) as term,a.custid,isnull(a.ttlpj,0) as total,
                    isnull((select isnull(sum(x.amount),0) from cftrkkbbdt x inner join cftrkkbbhd y on x.voucherid=y.voucherid 
                    where x.note=a.saleid and y.actor=a.custid and x.rekeningid=(select drpj from samsset)
                    and convert(varchar(8),y.transdate,112) <= :transdate1 ),0) as bayar 
                    from artrpenjualanhd a inner join armscustomer b on a.custid=b.custid
            ) as k 
            inner join armscustomer l on k.custid=l.custid 
            where k.custid=:custid and 
            isnull(k.total-k.bayar,0) <> 0
            group by l.limitpiutang,k.custid
            ) as x
            group by x.sisalimit,x.nilaisobaru,x.overdue",
            [
                'soid' => $param['soid'],
                'custid' => $param['custid'],
                'transdate' => $param['transdate'],
                'transdate1' => $param['transdate']
            ]
        );

        return $result;
    }

    function hitungTotal($param)
    {

        $result = DB::selectONe(
            'SELECT b.poid,isnull(sum(qty*(price+bagasi)),0) as subtotal,
            isnull(sum(qty*(price+bagasi))*0.01*b.ppn,0) as ppn,
            isnull(sum(qty*(price+bagasi))*(100+b.ppn)*0.01,0) as total,
            isnull(sum(qty*(price+bagasi))*(100+b.ppn)*0.01,0)+isnull(b.administrasi,0) as grandtotal,
            isnull(sum(qty*((price)-modal)),0) as margin,
            case when sum(qty*modal) = 0 then 100 else (sum(qty*(price-modal))/sum(qty*modal)*100) end as pmargin
            from artrpurchaseorderdt a
            inner join artrpurchaseorderhd b on a.poid=b.poid
            where b.poid=:soid
            group by b.administrasi,b.ppn,b.poid
            ',
            [
                'soid' => $param['soid']
            ]
        );

        return $result;
    }

    function updateTotal($param)
    {
        $result = DB::update(
            'UPDATE artrpurchaseorderhd SET ttlso = :grandtotal WHERE poid = :soid',
            [
                'soid' => $param['soid'],
                'grandtotal' => $param['grandtotal']
            ]
        );

        return $result;
    }

    function updateJenis($param)
    {
        $result = DB::update(
            'UPDATE artrpurchaseorderhd SET jenis = :jenis , otoby = :upduser, otodate = getdate() WHERE poid = :soid',
            [
                'soid' => $param['soid'],
                'jenis' => $param['jenis'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    function cekSalesorder($soid)
    {
        $result = DB::selectOne(
            'SELECT * from artrpurchaseorderhd WHERE poid = :soid',
            [
                'soid' => $soid
            ]
        );

        return $result;
    }
}

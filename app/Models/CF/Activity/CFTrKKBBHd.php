<?php

namespace App\Models\CF\Activity; //1

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class CFTrKKBBHd extends BaseModel //nama class
{
    use HasFactory;

    protected $table = 'cftrkkbbhd'; //sesuaiin nama tabel

    public $timestamps = false;
    
    public static $rulesInsert = [
        'transdate' => 'required',
        'transdate1' => 'required'
    ];

    public static $rulesUpdateAll = [
        'transdate' => 'required',
        'transdate1' => 'required'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO cftrkkbbhd
            (voucherid,transdate,actor,bankid,note,flagkkbb,upddate,upduser,currid,jumlahd,jumlahk,nobgcek,fgpayment,voucherno,userubah,tglubah,transdate1,rate) 
            VALUES 
            (:voucherid,:transdate,:actor,:bankid,:note,:flagkkbb,getDate(),:upduser,:currid,:jumlahd,:jumlahk,:nobgcek,'T',:voucherno,:userubah,getdate(),:transdate1,1)", 
            [
            'voucherid' => $param['voucherid'],
            'transdate' => $param['transdate'],
            'actor' => $param['actor'],
            'bankid' => $param['bankid'],
            'note' => $param['note'],
            'flagkkbb' => $param['flagkkbb'],
            'upduser' => $param['upduser'],
            'currid' => $param['currid'],
            'jumlahd' => $param['total'],
            'jumlahk' => $param['total'],
            'nobgcek' => $param['nobgcek'],
            'voucherno' => $param['voucherid'],
            'userubah' => $param['upduser'],
            'transdate1' => $param['transdate1']
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
            "SELECT 
            case when a.flagkkbb in ('apb','arb','bm','bk') then 'Y' else 'T' end as txnbank,
            case when a.FlagKKBB in ('ark','arc','arb','apk','apb','apc') then 'T'
	        when a.FlagKKBB in ('ju') then 'X' else 'Y' end as userinput,
            case when a.FlagKKBB in ('arc','apc') then 'Y' else 'T' end as dualtgl, 
            a.voucherid,a.transdate,a.actor,
            case when a.FlagKKBB in ('ark','arb','arc') then (select x.custname from ARMsCustomer x where x.custid=a.actor)
                 when a.FlagKKBB in ('apk','apb','apc') then (select x.suppname from apmssupplier x where x.suppid=a.actor)
                 else a.actor end as actorname,
            a.bankid,a.note,a.upddate,a.upduser,a.currid,a.jumlahd as total,
            a.nobgcek,a.fgpayment,a.voucherno,a.userubah,a.tglubah,a.transdate1,a.rate 
            from cftrkkbbhd a where convert(varchar(10),a.transdate1,112) between :dari and :sampai and a.flagkkbb=:fgtrans 
            and isnull(a.bankid,'') like :bankid and isnull(a.actor,'') like :actorkeyword and a.voucherid like :voucherkeyword 
            order by a.transdate $order",
            [
                'dari' => $param['dari'],
                'sampai' => $param['sampai'],
                'fgtrans' => $param['fgtrans'],
                'bankid' => '%' . $param['bankid'] . '%',
                'actorkeyword' => '%' . $param['actorkeyword'] . '%',
                'voucherkeyword' => '%' . $param['voucherkeyword'] . '%',
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT 
            case when a.flagkkbb in ('apb','arb','bm','bk') then 'Y' else 'T' end as txnbank,
            case when a.FlagKKBB in ('ark','arc','arb','apk','apb','apc') then 'T'
	        when a.FlagKKBB in ('ju') then 'X' else 'Y' end as userinput,
            a.voucherid,a.transdate,a.actor,
            case when a.FlagKKBB in ('ark','arb','arc') then (select x.custname from ARMsCustomer x where x.custid=a.actor)
                 when a.FlagKKBB in ('apk','apb','apc') then (select x.suppname from apmssupplier x where x.suppid=a.actor)
                 else a.actor end as actorname,
            a.bankid,a.note,a.upddate,a.upduser,a.currid,a.jumlahd as total,
            a.nobgcek,a.fgpayment,a.voucherno,a.userubah,a.tglubah,a.transdate1,a.rate 
            from cftrkkbbhd a WHERE a.voucherid = :voucherid ",
            [
                'voucherid' => $param['voucherid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE cftrkkbbhd SET 
                transdate = :transdate,
                actor = :actor,
                bankid = :bankid,
                note = :note,
                currid = :currid,
                jumlahd = :jumlahd,
                jumlahk = :jumlahk,
                nobgcek = :nobgcek,
                userubah = :userubah,
                tglubah = getdate(),
                transdate1 = :transdate1
            WHERE voucherid = :voucherid ',
            [
            'voucherid' => $param['voucherid'],
            'transdate' => $param['transdate'],
            'actor' => $param['actor'],
            'bankid' => $param['bankid'],
            'note' => $param['note'],
            'currid' => $param['currid'],
            'jumlahd' => $param['total'],
            'jumlahk' => $param['total'],
            'nobgcek' => $param['nobgcek'],
            'userubah' => $param['upduser'],
            'transdate1' => $param['transdate1']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM cftrkkbbhd WHERE voucherid = :voucherid',
            [
                'voucherid' => $param['voucherid']
            ]
        );

        return $result;
    }

    function cekVoucher($voucherid)
    {
        $result = DB::selectOne(
            'SELECT * from cftrkkbbhd WHERE voucherid = :voucherid',
            [
                'voucherid' => $voucherid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($fgtrans, $transdate)
    {

        $year = substr($transdate, 2, 2);
        $month = substr($transdate, 4, 2);

        $autoNumber = $this->autoNumber($this->table, 'voucherid', $fgtrans.'/'.$year.'/'.$month.'-', '0000');

        return $autoNumber;
    }
}

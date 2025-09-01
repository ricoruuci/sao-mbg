<?php

namespace App\Models\CF\Activity; //1

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class CFTrKKBBDt extends Model //nama class
{
    use HasFactory;

    protected $table = 'cftrkkbbdt'; //sesuaiin nama tabel

    public $timestamps = false;
    
    public static $rulesCariNota = [
        'transdate' => 'required',
        //'actor' => 'required',
        'fgtrans' => 'required'
    ];

    public static $rulesInsert = [
        'detail.*.rekeningid' => 'required',
        'detail.*.note' => 'required',
        'detail.*.amount' => 'required',
        'detail.*.jenis' => 'required'
    ];

    public static $messagesInsert = [
        'detail.*.rekeningid.required' => 'Kolom Rekening (posisi index ke-:index) harus diisi.',
        'detail.*.note.required' => 'Kolom Keterangan (posisi index ke-:index) harus diisi.',
        'detail.*.amount.required' => 'Kolom Nominal (posisi index ke-:index) harus diisi.',
        'detail.*.jenis.required' => 'Kolom Jenis (posisi index ke-:index) harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO cftrkkbbdt
            (voucherid,rekeningid,note,amount,upddate,upduser,jenis) 
            VALUES 
            (:voucherid,:rekeningid,:note,:amount,getDate(),:upduser,:jenis)", 
            [
                'voucherid' => $param['voucherid'],
                'rekeningid' => $param['rekeningid'],
                'note' => $param['note'],
                'amount' => $param['amount'],
                'upduser' => $param['upduser'],
                'jenis' => $param['jenis']
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::select(
            "SELECT case when b.FlagKKBB in ('ark','arc','arb','apk','apb','apc') then 'T' else 'Y' end as userinput,
            a.voucherid,a.rekeningid,c.rekeningname,a.note,a.amount,a.upddate,a.upduser,a.jenis from cftrkkbbdt a
            inner join CFTrKKBBHd b on a.voucherid=b.voucherid
            left join cfmsrekening c on a.RekeningID=c.rekeningid WHERE a.voucherid = :voucherid ",
            [
                'voucherid' => $param['voucherid']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM cftrkkbbdt WHERE voucherid = :voucherid',
            [
                'voucherid' => $param['voucherid']
            ]
        );

        return $result;
    }

    function cariInvoiceBlmLunas($param)
    {
        
        if ($param['fgtrans']=='APK' or $param['fgtrans']=='APB' or $param['fgtrans']=='APC'){

            $result = DB::select(
                "SELECT k.suppid as actor,k.purchaseid as notaid,k.transdate,isnull(k.ttlpb,0) as total,isnull(k.bayar,0) as bayar,isnull(k.retur,0) as retur,
                isnull(k.ttlpb-k.bayar-k.retur,0) as sisa from (
                select b.purchaseid,b.transdate,b.ttlpb,b.currid,b.suppid,
                (select isnull(sum(amount),0) from cftrkkbbdt a inner join cftrkkbbhd c on a.voucherid=c.voucherid 
                where a.note=b.purchaseid and c.actor=b.suppid and a.rekeningid=(select drpb from samsset)) as bayar, (select isnull(sum(price*qty),0) 
                from aptrreturndt f inner join aptrreturnhd g  on f.returnid=g.returnid 
                where g.flagretur='b' and f.purchaseid=b.purchaseid  and g.suppid=b.suppid) as retur 
                from aptrpurchasehd b where isnull(b.fgoto,'t')='y' ) as k 
                where k.currid='idr' and k.suppid=:actor and 
                convert(varchar(10),k.transdate,112) <= :transdate 
                and isnull(k.ttlpb-k.bayar-k.retur,0) > 0 order by k.transdate",
                [
                    'actor' => $param['actor'],
                    'transdate' => $param['transdate']
                ]
            );
        }
        else 
        {            
            $result = DB::select(
                "SELECT k.custid as actor,k.saleid as notaid,k.transdate,isnull(k.ttlpj,0) as total,isnull(k.bayar,0) as bayar,
                isnull(k.retur,0) as retur,isnull(k.ttlpj-k.bayar-k.retur,0) as sisa from (
                select saleid,transdate,b.currid,b.custid,isnull(ttlpj,0) as ttlpj,
                (select isnull(sum(a.amount),0) from cftrkkbbdt a where a.note=b.saleid and a.rekeningid=(select drpj from samsset)) as bayar,
                (select isnull(sum(x.qty*y.price),0) from artrreturpenjualandt x 
                inner join artrpenjualandt y on x.saleid=y.saleid and x.itemid=y.itemid where y.saleid=b.saleid) as retur from artrpenjualanhd b ) as k 
                where k.currid='idr' and k.custid=:actor 
                and isnull(k.ttlpj-k.bayar-k.retur,0) <> 0
                and convert(varchar(10),k.transdate,112) <= :transdate order by k.transdate",
                [
                    'actor' => $param['actor'],
                    'transdate' => $param['transdate']
                ]
            );
        }

        return $result;
    }
}

?>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class JualHd extends BaseModel
{
    use HasFactory;

    protected $table = 'trjualhd';

    public $timestamps = false;

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT TrJualHD (nota,tgljual,jamjual,ppn,disc,kdpos,nomeja,jmlorang,
            cashier,fgbayar,fgbatal,keterangan,paytype,disctype,upddate,upduser,charge,fgfromqb,kdmember,fgstatus,company_id)
            VALUES (:nota, :transdate, :transdate1, (select top 1 isnull(nmset,0) from setup where kdset='ppn'), 0, 'DL',
            :nomeja, 0, :cashier, 'T', 'T', :note, 0, 0, getdate(), :upduser, 0, 'T', '00000', :fgstatus,:company_id) ",
            [
                'nota' => $params['nota_jual'],
                'transdate' => $params['transdate'],
                'transdate1' => $params['transdate'], // Assuming jamjual is the same as transdate
                'nomeja' => $params['nomor_meja'],
                'cashier' => $params['cashier'],
                'note' => $params['note'],
                'upduser' => $params['upduser'],
                'fgstatus' => $params['fgstatus'],
                'company_id' => $params['company_id']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE TrJualHD
            SET
            tgljual = :transdate,
            jamjual = :transdate1,
            nomeja = :nomeja,
            cashier = :cashier,
            keterangan = :note,
            upddate = getdate(),
            upduser = :upduser,
            fgstatus = :fgstatus
            WHERE nota = :nota",
            [
                'nota' => $params['nota_jual'],
                'transdate' => $params['transdate'],
                'transdate1' => $params['transdate'],
                'nomeja' => $params['nomor_meja'],
                'cashier' => $params['cashier'],
                'note' => $params['note'],
                'upduser' => $params['upduser'],
                'fgstatus' => $params['fgstatus']
            ]
        );

        return $result;
    }

    function updateBatal($params)
    {
        $result = DB::update(
            "UPDATE TrJualHD
            SET
            fgbatal = 'Y',
            upddate = getdate(),
            upduser = :upduser
            WHERE nota = :nota",
            [
                'nota' => $params['nota_jual'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function updatePayment($params)
    {
        $result = DB::update(
            "UPDATE TrJualHD SET
            PayType = :paytype,
            FgBayar = 'Y',
            bankid = :bank_id
            WHERE nota = :nota",
            [
                'nota' => $params['nota_jual'],
                'paytype' => $params['paytype'],
                'bank_id' => $params['bank_id']
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.nota as nota_jual,
            a.tgljual as transdate,
            isnull(a.nomeja,'') as nomor_meja,
            a.cashier as cashier,
            isnull(a.keterangan,'') as note,
            a.ppn as ppn,
            isnull(a.stpj,0) as sub_total,
            isnull(a.ttltax,0) as total_ppn,
            isnull(a.ttlpj,0) as grand_total,
            a.fgbayar as fg_bayar,
            a.paytype as payment_type,
            isnull(a.fgstatus,'0') as fgstatus,
            case when isnull(a.fgstatus,'0')='0' then 'DINE IN'
                 when isnull(a.fgstatus,'0')='1' then 'TAKE AWAY'
                 when isnull(a.fgstatus,'0')='2' then 'DELIVERY'
                 else 'No Data' end as status_name,
            case when a.paytype='0' then 'QRIS' 
                 when a.paytype='1' then 'Debit Card'
                 when a.paytype='2' then 'Credit Card'
                 when a.paytype='3' then 'Cash'
                 else 'No Data' end as payment_type_name,
            a.upddate,
            a.upduser
            from trjualhd a
            left join msposisi b on a.kdpos=b.kdpos
            where a.nota = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function getDataMeja($params)
    {
        $result = DB::select(
            "SELECT distinct isnull(b.nomeja,'') as nomor_meja,b.nota as nota_jual,
            isnull(b.fgstatus,'0') as fgstatus,
            case when isnull(b.fgstatus,'0')='0' then 'DINE IN'
                 when isnull(b.fgstatus,'0')='1' then 'TAKE AWAY'
                 when isnull(b.fgstatus,'0')='2' then 'DELIVERY'
                 else 'No Data' end as status_name
            from trjualhd b
            where convert(varchar(8),b.tgljual,112) = :transdate and b.fgbayar='T' and b.fgbatal='T'
            and b.company_id =:company_id
            order by isnull(b.nomeja,'') ",
            [
                'company_id' => $params['company_id'],
                'transdate' => $params['transdate']
            ]
        );

        return $result;
    }

    function cekMejaAktif($id,$company_id,$transdate)
    {
        $result = DB::selectOne(
            "SELECT distinct isnull(b.nomeja,'') as nomor_meja,b.nota as nota_jual,
            isnull(b.fgstatus,'0') as fgstatus,
            case when isnull(b.fgstatus,'0')='0' then 'DINE IN'
                 when isnull(b.fgstatus,'0')='1' then 'TAKE AWAY'
                 when isnull(b.fgstatus,'0')='2' then 'DELIVERY'
                 else 'No Data' end as status_name
            from trjualhd b
            where convert(varchar(8),b.tgljual,112) = :transdate and b.fgbayar='T' and b.fgbatal='T'
            and b.company_id=:company_id
            and isnull(b.nomeja,'')=:id ",
            [
                'id' => $id,
                'company_id' => $company_id,
                'transdate' => $transdate
            ]
        );

        return $result;
    }

    function hitungTotal($id)
    {
        $result = DB::selectOne(
            "SELECT subtotal,  diskon
            ,(subtotal - diskon) * charge / 100 as sc
            ,(subtotal - diskon + ((subtotal - diskon) * charge / 100)) * ppn / 100 as pajak
            ,subtotal + ((subtotal - diskon + ((subtotal - diskon) * charge / 100)) * ppn / 100) - diskon + ((subtotal - diskon) * charge / 100) as grandtotal
            from
            (
            select sum(a.jumlah*a.harga) as subtotal
            ,sum(a.jumlah*a.harga*a.disc/100)  as diskon
            , b.disc, b.ppn, b.charge
            from trjualdt a
            inner join trjualhd b on a.nota=b.nota
            where a.nota=:id
            group by b.disc, b.ppn, b.charge
            ) a",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function updateTotal($params)
    {
        $result = DB::update(
            "UPDATE trjualhd
            SET stpj = :subtotal,
            ttlpj = :grandtotal,
            ttltax = :pajak
            where nota=:id ",
            [
                'subtotal' => $params['sub_total'],
                'grandtotal' => $params['grand_total'],
                'pajak' => $params['total_ppn'],
                'id' => $params['nota_jual']
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from trjualhd WHERE nota = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($transdate,$company_code)
    {
        $autoNumber = $this->autoNumber($this->table, 'nota', $company_code.$transdate, '0000');

        return $autoNumber;
    }

}

?>

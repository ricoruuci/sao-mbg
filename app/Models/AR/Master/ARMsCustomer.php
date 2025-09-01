<?php

namespace App\Models\AR\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class ARMsCustomer extends BaseModel
{
    use HasFactory;

    protected $table = 'armscustomer';

    public $timestamps = false;

    public static $rulesInsert = [
        'custname' => 'required',
        'custtype' => 'required'
    ];

    public static $rulesUpdateAll = [
        'custname' => 'required',
        'custtype' => 'required'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO armscustomer
            (custid,custname,address,city,phone,fax,email,note,custtype,upddate,upduser,limitpiutang,limitasli,fgkoma,up,term,salesid,kodefp) 
            VALUES 
            (:custid,:custname,:address,:city,:phone,:email,:npwp,:note,:custtype,getdate(),:upduser,:limitpiutang,:limitasli,:fgkoma,:up,:term,:salesid,:kodefp)",
            [
                'custid' => $param['custid'],
                'custname' => $param['custname'],
                'address' => $param['address'],
                'city' => $param['city'],
                'phone' => $param['phone'],
                'email' => $param['email'],
                'npwp' => $param['npwp'],
                'note' => $param['note'],
                'custtype' => $param['custtype'],
                'upduser' => $param['upduser'],
                'limitpiutang' => $param['limitpiutang'],
                'limitasli' => $param['limitasli'],
                'fgkoma' => $param['fgkoma'],
                'up' => $param['up'],
                'term' => $param['term'],
                'salesid' => $param['salesid'],
                'kodefp' => $param['kodefp']
            ]
        );

        return $result;
    }

    function getListData($param)
    {
        if ($param['sortby'] == 'custid') {
            $order = 'a.custid';
        } else {
            $order = 'a.custname';
        }
        $result = DB::select(
            "SELECT a.custid,a.custname,a.address,a.up,a.city,a.phone,a.fax as email,a.email as npwp,a.salesid,
            isnull((select b.salesname from armssales b where b.salesid=a.salesid),'') as salesname,
            a.limitpiutang,a.term,a.note,a.kodefp,a.custtype,a.fgkoma,a.upddate,a.upduser 
            from armscustomer a where a.custid like :custidkeyword and a.custname like :custnamekeyword
            order by $order ",
            [
                'custidkeyword' => '%' . $param['custidkeyword'] . '%',
                'custnamekeyword' => '%' . $param['custnamekeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT a.custid,a.custname,a.address,a.up,a.city,a.phone,a.fax as email,a.email as npwp,a.salesid,
            isnull((select b.salesname from armssales b where b.salesid=a.salesid),'') as salesname,
            a.limitpiutang,a.term,a.note,a.kodefp,a.custtype,a.fgkoma,a.upddate,a.upduser 
            from armscustomer a WHERE a.custid = :custid ",
            [
                'custid' => $param['custid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE armscustomer SET 
            custname = :custname, 
            address = :address,
            city = :city,
            phone = :phone,
            fax = :email,
            email = :npwp,
            note = :note,
            custtype = :custtype,
            upddate = getdate(), 
            upduser = :upduser,
            limitpiutang = :limitpiutang,
            limitasli =:limitasli,
            fgkoma = :fgkoma,
            up = :up,
            term = :term,
            salesid = :salesid,
            kodefp = :kodefp
            WHERE custid = :custid',
            [
                'custid' => $param['custid'],
                'custname' => $param['custname'],
                'address' => $param['address'],
                'city' => $param['city'],
                'phone' => $param['phone'],
                'email' => $param['email'],
                'npwp' => $param['npwp'],
                'note' => $param['note'],
                'custtype' => $param['custtype'],
                'upduser' => $param['upduser'],
                'limitpiutang' => $param['limitpiutang'],
                'limitasli' => $param['limitasli'],
                'fgkoma' => $param['fgkoma'],
                'up' => $param['up'],
                'term' => $param['term'],
                'salesid' => $param['salesid'],
                'kodefp' => $param['kodefp']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM armscustomer WHERE custid = :custid',
            [
                'custid' => $param['custid']
            ]
        );

        return $result;
    }

    function cekCustomer($custid)
    {

        $result = DB::selectOne(
            'SELECT * from armscustomer WHERE custid = :custid',
            [
                'custid' => $custid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($custtype, $custname)
    {

        $kode = substr($custtype, 0, 1);

        $nama = substr($custname, 0, 1);

        $autoNumber = $this->autoNumber($this->table, 'custid', $kode . $nama, '000');

        return $autoNumber;
    }
}

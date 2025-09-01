<?php

namespace App\Models\AP\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class APMsSupplier extends BaseModel
{
    use HasFactory;

    protected $table = 'apmssupplier';

    public $timestamps = false;
    
    public static $rulesInsert = [
        'suppname' => 'required'
    ];

    public static $rulesUpdateAll = [
        'suppname' => 'required'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO apmssupplier
            (suppid, suppname, address, city, contactperson, phone, fax, email, note, upddate, upduser) 
            VALUES 
            (:suppid, :suppname, :address, :city, :contactperson, :phone, :fax, :email, :note, getdate(), :upduser)", 
            [
                'suppid' => $param['suppid'],
                'suppname' => $param['suppname'],
                'address' => $param['address'],
                'city' => $param['city'],
                'contactperson' => $param['contactperson'],
                'phone' => $param['phone'],
                'fax' => $param['fax'],
                'email' => $param['email'],
                'note' => $param['note'],
                'upduser' => $param['upduser']
            ]
        );        

        return $result;
    }

    function getListData($param)
    {
        if($param['sortby']=='suppid')
        {
            $order = 'suppid';
        }
        else 
        {
            $order = 'suppname';
        }
        $result = DB::select(
            "SELECT suppid, suppname, address, city, contactperson, phone, fax, email, note, upddate, upduser from apmssupplier 
            where suppid like :suppidkeyword and suppname like :suppnamekeyword order by $order",
            [
                'suppidkeyword' => '%'.$param['suppidkeyword'].'%',
                'suppnamekeyword' => '%'.$param['suppnamekeyword'].'%'
            ]
        );

        return $result;
    }

    function getData($param)
    {

        $result = DB::selectOne(
            'SELECT suppid, suppname, address, city, contactperson, phone, fax, email, note, upddate, upduser from apmssupplier WHERE suppid = :suppid',
            [
                'suppid' => $param['suppid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE apmssupplier SET 
            suppname = :suppname, 
            address = :address,
            city = :city,
            contactperson = :contactperson,
            phone = :phone,
            fax = :fax,
            email = :email,
            note = :note,
            upddate = getdate(), 
            upduser = :upduser 
            WHERE suppid = :suppid',
            [
                'suppid' => $param['suppid'],
                'suppname' => $param['suppname'],
                'address' => $param['address'],
                'city' => $param['city'],
                'contactperson' => $param['contactperson'],
                'phone' => $param['phone'],
                'fax' => $param['fax'],
                'email' => $param['email'],
                'note' => $param['note'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM apmssupplier WHERE suppid = :suppid',
            [
                'suppid' => $param['suppid']
            ]
        );

        return $result;
    }

    function cekSupplier($suppid)
    {

        $result = DB::selectOne(
            'SELECT * from apmssupplier WHERE suppid = :suppid',
            [
                'suppid' => $suppid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($suppname)
    {

        $kode = 'S';

        $nama = substr($suppname,0,1);

        $autoNumber = $this->autoNumber($this->table, 'suppid', $kode.$nama, '000');

        return $autoNumber;
    }
}
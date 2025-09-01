<?php

namespace App\Models\AR\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class ARMsSales extends BaseModel
{
    use HasFactory;

    protected $table = 'armssales';

    public $timestamps = false;

    public static $rulesInsert = [
        'salesname' => 'required'
    ];

    public static $rulesUpdateAll = [
        'salesname' => 'required'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO armssales
            (salesid,salesname,address,phone,hp,email,note,upddate,upduser,jabatan,uangmakan,
            uangbulanan,fgactive,tglgabung,limitkasbon,kerajinan,tomzet,kom1,kom2,kom3,kom4) 
            VALUES 
            (:salesid,:salesname,:address,:phone,:hp,:email,:note,getdate(),:upduser,:jabatan,:uangmakan,
            :uangbulanan,:fgactive,:tglgabung,:limitkasbon,:kerajinan,:tomzet,:kom1,:kom2,:kom3,:kom4)",
            [
                'salesid' => $param['salesid'],
                'salesname' => $param['salesname'],
                'address' => $param['address'],
                'phone' => $param['phone'],
                'hp' => $param['hp'],
                'email' => $param['email'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'jabatan' => $param['jabatan'],
                'uangmakan' => $param['uangmakan'],
                'uangbulanan' => $param['uangbulanan'],
                'fgactive' => $param['fgactive'],
                'tglgabung' => $param['tglgabung'],
                'limitkasbon' => $param['limitkasbon'],
                'kerajinan' => $param['kerajinan'],
                'tomzet' => $param['tomzet'],
                'kom1' => $param['kom1'],
                'kom2' => $param['kom2'],
                'kom3' => $param['kom3'],
                'kom4' => $param['kom4']
            ]
        );

        return $result;
    }

    function getListData($param)
    {
        if ($param['sortby'] == 'salesid') {
            $order = 'a.salesid';
        } else {
            $order = 'a.salesname';
        }
        $result = DB::select(
            "SELECT salesid,salesname,tglgabung,jabatan,address,phone,hp,email,note,tomzet,kom1,kom2,kom3,kom4,fgactive,
            upddate,upduser,limitkasbon,kerajinan,uangmakan,uangbulanan from armssales a
            where salesid like :salesidkeyword and salesname like :salesnamekeyword
             order by $order",
            [
                'salesidkeyword' => '%' . $param['salesidkeyword'] . '%',
                'salesnamekeyword' => '%' . $param['salesnamekeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            'SELECT salesid,salesname,tglgabung,jabatan,address,phone,hp,email,note,tomzet,kom1,kom2,kom3,kom4,fgactive,
                    upddate,upduser,limitkasbon,kerajinan,uangmakan,uangbulanan from armssales WHERE salesid = :salesid',
            [
                'salesid' => $param['salesid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE armssales SET 
            salesname = :salesname,
            address = :address,
            phone = :phone,
            hp = :hp,
            email = :email,
            note = :note,
            upddate = getdate(),
            upduser = :upduser,
            jabatan = :jabatan,
            uangmakan = :uangmakan,
            uangbulanan = :uangbulanan,
            fgactive = :fgactive,
            tglgabung = :tglgabung,
            limitkasbon = :limitkasbon,
            kerajinan = :kerajinan,
            tomzet = :tomzet,
            kom1 = :kom1,
            kom2 = :kom2,
            kom3 = :kom3,
            kom4 = :kom4
            WHERE salesid = :salesid',
            [
                'salesid' => $param['salesid'],
                'salesname' => $param['salesname'],
                'address' => $param['address'],
                'phone' => $param['phone'],
                'hp' => $param['hp'],
                'email' => $param['email'],
                'note' => $param['note'],
                'upduser' => $param['upduser'],
                'jabatan' => $param['jabatan'],
                'uangmakan' => $param['uangmakan'],
                'uangbulanan' => $param['uangbulanan'],
                'fgactive' => $param['fgactive'],
                'tglgabung' => $param['tglgabung'],
                'limitkasbon' => $param['limitkasbon'],
                'kerajinan' => $param['kerajinan'],
                'tomzet' => $param['tomzet'],
                'kom1' => $param['kom1'],
                'kom2' => $param['kom2'],
                'kom3' => $param['kom3'],
                'kom4' => $param['kom4']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM armssales WHERE salesid = :salesid',
            [
                'salesid' => $param['salesid']
            ]
        );

        return $result;
    }

    function cekSales($salesid)
    {

        $result = DB::selectOne(
            'SELECT * from armssales WHERE salesid = :salesid',
            [
                'salesid' => $salesid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($salesname)
    {
        $nama = substr($salesname, 0, 1);

        $autoNumber = $this->autoNumber($this->table, 'salesid', $nama, '000');

        return $autoNumber;
    }
}

<?php

namespace App\Models\CF\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class MsGroupRek extends BaseModel
{
    use HasFactory;

    protected $table = 'cfmsgrouprek';

    public $timestamps = false;

    public static $rulesInsert = [
        'grouprekname' => 'required',
        'kode' => 'required'
    ];

    public static $messagesInsert = [
        'grouprekname' => 'Kolom nama group rekening harus diisi.',
        'kode' => 'Kolom kode harus diisi.'
    ];

    public static $rulesUpdateAll = [
        'grouprekid' => 'required',
        'grouprekname' => 'required'
    ];

    public static $messagesUpdate = [
        'grouprekid' => 'Kode group rekening tidak ditemukan.',
        'grouprekname' => 'Kolom nama group rekening harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO cfmsgrouprek 
             (grouprekid,grouprekname,upddate,upduser,kode) 
             VALUES 
             (:grouprekid,:grouprekname,getdate(),:upduser,:kode)",
            [
                'grouprekid' => $param['grouprekid'],
                'grouprekname' => $param['grouprekname'],
                'upduser' => $param['upduser'],
                'kode' => $param['kode']
            ]
        );

        return $result;
    }

    function getListData($param)
    {
        if ($param['sortby'] == 'id') {
            $order = 'a.grouprekid';
        } else {
            $order = 'a.grouprekname';
        }

        if ($param['jeniskeyword'] == 'all') {
            $jeniskeyword = '';
        } else {
            $jeniskeyword = $param['jeniskeyword'];
        }

        $result = DB::select(
            "SELECT a.grouprekid,a.grouprekname,a.upddate,a.upduser,a.kode from cfmsgrouprek a 
            where isnull(a.grouprekname,'') like :groupreknamekeyword and isnull(a.kode,'') like :jeniskeyword 
            and isnull(grouprekid,'') like :grouprekidkeyword order by $order ",
            [
                'groupreknamekeyword' => '%' . $param['groupreknamekeyword'] . '%',
                'jeniskeyword' => '%' . $jeniskeyword . '%',
                'grouprekidkeyword' => '%' . $param['grouprekidkeyword'] . '%',
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT a.grouprekid,a.grouprekname,a.upddate,a.upduser,a.kode from cfmsgrouprek a WHERE a.grouprekid = :grouprekid ",
            [
                'grouprekid' => $param['grouprekid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE cfmsgrouprek SET 
            grouprekname = :grouprekname,
            upddate = getdate(), 
            upduser = :upduser
            WHERE grouprekid = :grouprekid',
            [
                'grouprekid' => $param['grouprekid'],
                'grouprekname' => $param['grouprekname'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM cfmsgrouprek WHERE grouprekid = :grouprekid',
            [
                'grouprekid' => $param['grouprekid']
            ]
        );

        return $result;
    }

    function cekGroupRek($grouprekid)
    {

        $result = DB::selectOne(
            'SELECT * from cfmsgrouprek WHERE grouprekid = :grouprekid',
            [
                'grouprekid' => $grouprekid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($kode)
    {
        $autoNumber = $this->autoNumber($this->table, 'grouprekid', $kode . '.', '000');

        return $autoNumber;
    }
}

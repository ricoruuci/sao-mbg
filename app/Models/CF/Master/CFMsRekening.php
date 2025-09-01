<?php

namespace App\Models\CF\Master; //1

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;

use function PHPUnit\Framework\isNull;

class CFMsRekening extends BaseModel //nama class
{
    use HasFactory;

    protected $table = 'cfmsrekening';

    public $timestamps = false;

    public static $rulesInsert = [
        'rekeningname' => 'required',

    ];

    public static $messagesInsert = [
        'rekeningname' => 'Kolom nama rekening harus diisi.',
        'grouprekid' => 'Kolom group rekening harus diisi.'
    ];

    public static $rulesUpdateAll = [
        'rekeningid' => 'required',
        'rekeningname' => 'required',
        'grouprekid' => 'required'
    ];

    public static $messagesUpdate = [
        'rekeningid' => 'Kode rekening tidak ditemukan.',
        'rekeningname' => 'Kolom nama rekening harus diisi.',
        'grouprekid' => 'Kolom group rekening harus diisi.'
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO cfmsrekening 
             (rekeningid,rekeningname,grouprekid,upddate,upduser,tipe,fgtipe,fgactive) 
             VALUES 
             (:rekeningid,:rekeningname,:grouprekid,getdate(),:upduser,:tipe,:fgtipe,:fgactive)",
            [
                'rekeningid' => $param['rekeningid'],
                'rekeningname' => $param['rekeningname'],
                'grouprekid' => $param['grouprekid'],
                'upduser' => $param['upduser'],
                'tipe' => $param['tipe'],
                'fgtipe' => $param['fgtipe'],
                'fgactive' => $param['fgactive'],
            ]
        );

        return $result;
    }



    function getListData($param)
    {
        $result = DB::select(
            "SELECT a.rekeningid,a.rekeningname,a.grouprekid,b.grouprekname,a.note,a.fgactive from cfmsrekening a
            left join cfmsgrouprek b on a.grouprekid=b.grouprekid where a.fgactive='Y' and a.rekeningid like :rekeningidkeyword
            and isnull(a.rekeningname,'') like :rekeningnamekeyword and isnull(a.grouprekid,'') like :grouprekidkeyword and isnull(b.grouprekname,'') like :groupreknamekeyword ",
            [
                'rekeningidkeyword' => '%' . $param['rekeningidkeyword'] . '%',
                'rekeningnamekeyword' => '%' . $param['rekeningnamekeyword'] . '%',
                'grouprekidkeyword' => '%' . $param['grouprekidkeyword'] . '%',
                'groupreknamekeyword' => '%' . $param['groupreknamekeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::select(
            "SELECT a.rekeningid,a.rekeningname,a.grouprekid,b.grouprekname,a.note,a.fgactive from cfmsrekening a
            left join cfmsgrouprek b on a.grouprekid=b.grouprekid where a.fgactive='Y' and a.rekeningid= :rekeningid ",
            [
                'rekeningid' => $param['rekeningid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE cfmsrekening SET 
            rekeningname = :rekeningname,
            upddate = getdate(), 
            upduser = :upduser,
            grouprekid = :grouprekid,
            tipe = :tipe,
            fgtipe = :fgtipe
            WHERE rekeningid = :rekeningid',
            [
                'rekeningid' => $param['rekeningid'],
                'rekeningname' => $param['rekeningname'],
                'grouprekid' => $param['grouprekid'],
                'upduser' => $param['upduser'],
                'tipe' => $param['tipe'],
                'fgtipe' => $param['fgtipe']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM cfmsrekening WHERE rekeningid = :rekeningid',
            [
                'rekeningid' => $param['rekeningid']
            ]
        );

        return $result;
    }

    function cekRekening($rekeningid)
    {

        $result = DB::selectOne(
            'SELECT * from cfmsrekening WHERE rekeningid = :rekeningid',
            [
                'rekeningid' => $rekeningid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($kode)
    {
        $autoNumber = $this->autoNumber($this->table, 'rekeningid', $kode . '.', '000');

        return $autoNumber;
    }
}

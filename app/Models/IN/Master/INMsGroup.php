<?php

namespace App\Models\IN\Master;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class INMsGroup extends BaseModel
{
    use HasFactory;

    protected $table = 'innmsgroup';

    public $timestamps = false;

    public static $rulesInsert = [
        'groupid' => 'required',
        'groupdesc' => 'required',
    ];

    public static $messagesInsert = [
        'groupid' => 'kode group harus diisi.',
        'groupdesc' => 'Kolom nama group harus diisi !',
    ];

    public static $rulesUpdateAll = [
        'groupid' => 'required',
        'groupdesc' => 'required',
    ];

    public static $messagesUpdate = [
        'groupid' => 'kode group tidak ditemukan.',
        'groupdesc' => 'nama group harus diisi.',
    ];

    function getListData($param)
    {
        $result = DB::select(
            "SELECT groupid,groupdesc,upddate,upduser from inmsgroup where isnull(groupid,'') like :groupidkeyword and isnull(groupdesc,'') like :groupdesckeyword
            order by groupdesc",
            [
                'groupidkeyword' =>  '%' . $param['groupidkeyword'] . '%',
                'groupdesckeyword' => '%' . $param['groupdesckeyword'] . '%'
            ]
        );

        return $result;
    }

    function insertData($param)
    {
        $result = DB::insert(
            "INSERT INMsGroup (groupid,groupdesc,upddate,upduser)
            values (:groupid,:groupdesc,getdate(),:upduser)",
            [
                'groupid' =>   $param['groupid'],
                'groupdesc' =>  $param['groupdesc'],
                'upduser' =>  $param['upduser']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::insert(
            "UPDATE INMsGroup set
            groupdesc = :groupdesc,
            upddate = getdate(),
            upduser = :upduser
            where groupid = :groupid",
            [
                'groupid' =>   $param['groupid'],
                'groupdesc' =>  $param['groupdesc'],
                'upduser' =>  $param['upduser']
            ]
        );

        return $result;
    }

    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM INMsGroup WHERE groupid = :groupid',
            [
                'groupid' => $param['groupid']
            ]
        );

        return $result;
    }

    function cekGroup($groupid)
    {

        $result = DB::selectOne(
            'SELECT * from inmsgroup WHERE groupid = :groupid',
            [
                'groupid' => $groupid
            ]
        );

        return $result;
    }
}

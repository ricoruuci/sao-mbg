<?php

namespace App\Models\IN\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class INMsItem extends BaseModel
{
    use HasFactory;

    protected $table = 'inmsitem';

    public $timestamps = false;

    public static $rulesInsert = [
        'itemname' => 'required',
        'productid' => 'required',
        'groupid' => 'required',
    ];

    public static $rulesUpdateAll = [
        'itemname' => 'required',
        'productid' => 'required',
        'groupid' => 'required',
    ];

    public function insertData($param)
    {

        $result = DB::insert(
            "INSERT INTO inmsitem
            (itemid,itemname,productid,groupid,tahun,uomid,userprice,ctk,fgactive,note,upduser,upddate)
            VALUES
            (:itemid,:itemname,:productid,:groupid,:tahun,'Unit',0,'Y','Y',:note,:upduser,getdate())",
            [
                'itemid' => $param['itemid'],
                'itemname' => $param['itemname'],
                'productid' => $param['productid'],
                'groupid' => $param['groupid'],
                'upduser' => $param['upduser'],
                'tahun' => $param['tahun'],
                'note' => $param['note'],
                'upduser' => $param['upduser']
            ]
        );

        return $result;
    }

    function getListData($param)
    {
        if ($param['sortby'] == 'itemid') {
            $order = 'a.itemid';
        } else {
            $order = 'a.itemname';
        }
        $result = DB::select(
            "SELECT a.itemid,a.itemname,a.productid,c.productdesc,a.groupid,b.groupdesc,isnull(a.tahun,'') as tahun,a.uomid,isnull(a.note,'') as note
            from inmsitem a
            inner join inmsgroup b on a.groupid=b.groupid
            inner join inmsproduct c on c.productid=a.productid
            where isnull(a.itemid,'') like :itemidkeyword and isnull(a.itemname,'') like :itemnamekeyword
            and isnull(a.groupid,'') like :groupidkeyword and isnull(b.groupdesc,'') like :groupdesckeyword
            and isnull(a.productid,'') like :productidkeyword and isnull(c.productdesc,'') like :productdesckeyword
            order by $order ",
            [
                'itemidkeyword' => '%' . $param['itemidkeyword'] . '%',
                'itemnamekeyword' => '%' . $param['itemnamekeyword'] . '%',
                'groupidkeyword' => '%' . $param['groupidkeyword'] . '%',
                'groupdesckeyword' => '%' . $param['groupdesckeyword'] . '%',
                'productidkeyword' => '%' . $param['productidkeyword'] . '%',
                'productdesckeyword' => '%' . $param['productdesckeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {
        $result = DB::selectOne(
            "SELECT a.itemid,a.itemname,a.productid,c.productdesc,a.groupid,b.groupdesc,isnull(a.tahun,'') as tahun,a.uomid,isnull(a.note,'') as note
            from inmsitem a
            inner join inmsgroup b on a.groupid=b.groupid
            inner join inmsproduct c on c.productid=a.productid WHERE itemid = :itemid",
            [
                'itemid' => $param['itemid']
            ]
        );

        return $result;
    }

    function updateAllData($param)
    {
        $result = DB::update(
            'UPDATE inmsitem SET 
            itemname = :itemname,
            productid = :productid,
            groupid = :groupid,
            upddate = getdate(),
            upduser = :upduser,
            note = :note,
            tahun = :tahun
            WHERE itemid = :itemid ',
            [
                'itemid' => $param['itemid'],
                'itemname' => $param['itemname'],
                'productid' => $param['productid'],
                'groupid' => $param['groupid'],
                'upduser' => $param['upduser'],
                'note' => $param['note'],
                'tahun' => $param['tahun']
            ]
        );

        return $result;
    }


    function deleteData($param)
    {

        $result = DB::delete(
            'DELETE FROM inmsitem WHERE itemid = :itemid',
            [
                'itemid' => $param['itemid']
            ]
        );

        return $result;
    }

    function cekBarang($itemid)
    {

        $result = DB::selectOne(
            'SELECT * from inmsitem WHERE itemid = :itemid',
            [
                'itemid' => $itemid
            ]
        );

        return $result;
    }

    public function beforeAutoNumber($groupid, $productid)
    {

        $autoNumber = $this->autoNumber($this->table, 'itemid', $groupid . '.' . $productid . '.', '000');

        return $autoNumber;
    }
}

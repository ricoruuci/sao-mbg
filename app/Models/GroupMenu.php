<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class GroupMenu extends BaseModel
{
    use HasFactory;

    protected $table = 'msgroupmenu';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT kdgroupmenu as group_menu_id,nmgroupmenu as group_menu_name,upddate,upduser 
            from msgroupmenu 
            where nmgroupmenu like :search_keyword
            order by kdgroupmenu ",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT kdgroupmenu as group_menu_id,nmgroupmenu as group_menu_name,upddate,upduser 
            from msgroupmenu 
            where kdgroupmenu = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from msgroupmenu WHERE kdgroupmenu = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekTerpakai($id)
    {
        $result = DB::selectOne(
            'SELECT * from msmenuhd WHERE kdgroupmenu = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msgroupmenu (kdgroupmenu,nmgroupmenu,upddate,upduser) 
            VALUES (:kdgroupmenu, :nmgroupmenu, getdate(), :upduser)",
            [
                'kdgroupmenu' => $params['group_menu_id'],
                'nmgroupmenu' => $params['group_menu_name'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE msgroupmenu SET 
            nmgroupmenu = :nmgroupmenu, 
            upddate = getdate(), 
            upduser = :upduser 
            WHERE kdgroupmenu = :kdgroupmenu",
            [
                'kdgroupmenu' => $params['group_menu_id'],
                'nmgroupmenu' => $params['group_menu_name'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msgroupmenu WHERE kdgroupmenu = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'kdgroupmenu', 'GRM', '0000');

        return $autoNumber;
    }

}

?>

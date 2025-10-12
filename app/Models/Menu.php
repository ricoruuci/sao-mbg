<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Menu extends BaseModel
{
    use HasFactory;

    protected $table = 'msmenuhd';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT a.kdmenu as menu_id, a.nmmenu as menu_name, a.harga as price, a.jenis as fg_item,
            case when a.jenis='a' then 'makanan' else 'minuman' end as fg_item_name,
            a.kdgroupmenu as group_menu_id, b.nmgroupmenu as group_menu_name, a.filecontent as item_picture
            from msmenuhd a
            left join msgroupmenu b on a.kdgroupmenu=b.kdgroupmenu
            where a.nmmenu like :search_keyword
            order by a.kdmenu",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT a.kdmenu as menu_id, a.nmmenu as menu_name, a.harga as price, a.jenis as fg_item,
            case when a.jenis='a' then 'makanan' else 'minuman' end as fg_item_name,
            a.kdgroupmenu as group_menu_id, b.nmgroupmenu as group_menu_name, a.filecontent as item_picture
            from msmenuhd a
            left join msgroupmenu b on a.kdgroupmenu=b.kdgroupmenu
            where a.kdmenu = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function getDataByGroupMenuId($id)
    {
        $result = DB::select(
            "SELECT a.kdmenu as menu_id,a.nmmenu as menu_name,a.harga as price,a.jenis as fg_item,
            case when a.jenis='a' then 'makanan' else 'minuman' end as fg_item_name,
            a.kdgroupmenu as group_menu_id,b.nmgroupmenu as group_menu_name,a.filecontent as item_picture
            from msmenuhd a
            left join msgroupmenu b on a.kdgroupmenu=b.kdgroupmenu
            where a.kdgroupmenu = :id
            order by a.kdmenu ",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekData($id)
    {

        $result = DB::selectOne(
            'SELECT * from msmenuhd WHERE kdmenu = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function cekTerpakai($id)
    {

        $result = DB::selectOne(
            'SELECT * from trjualdt WHERE kdmenu = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO msmenuhd (kdmenu, nmmenu, harga, jenis, kdgroupmenu, filecontent, upddate, upduser) 
            VALUES (:kdmenu, :nmmenu, :harga, :jenis, :kdgroupmenu, :filecontent, getdate(), :upduser)",
            [
                'kdmenu' => $params['menu_id'],
                'nmmenu' => $params['menu_name'],
                'harga' => $params['price'],
                'jenis' => $params['fg_item'],
                'kdgroupmenu' => $params['group_menu_id'],
                'filecontent' => $params['item_picture'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function updateData($params)
    {
        $result = DB::update(
            "UPDATE msmenuhd SET 
            nmmenu = :nmmenu, 
            harga = :harga, 
            jenis = :jenis, 
            kdgroupmenu = :kdgroupmenu, 
            filecontent = :filecontent, 
            upddate = getdate(), 
            upduser = :upduser 
            WHERE kdmenu = :kdmenu",
            [
                'kdmenu' => $params['menu_id'],
                'nmmenu' => $params['menu_name'],
                'harga' => $params['price'],
                'jenis' => $params['fg_item'],
                'kdgroupmenu' => $params['group_menu_id'],
                'filecontent' => $params['item_picture'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM msmenuhd WHERE kdmenu = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'kdmenu', 'RM', '0000');

        return $autoNumber;
    }

}

?>

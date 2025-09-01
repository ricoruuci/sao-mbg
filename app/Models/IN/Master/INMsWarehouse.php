<?php

namespace App\Models\IN\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class INMsWarehouse extends Model
{
    use HasFactory;

    protected $table = 'inmswarehouse';

    public $timestamps = false;

    function getListData($param)
    {
        $result = DB::select(
            "SELECT warehouseid,warehousename,address,city,contactperson,phone,fax,upddate,upduser from inmswarehouse warehouseid like :warehouseidkeyword and 
            warehousename like :warehousenamekeyword order by warehouseid",
            [
                'warehouseidkeyword' => '%' . $param['warehouseidkeyword'] . '%',
                'warehousenamekeyword' => '%' . $param['warehousenamekeyword'] . '%'
            ]
        );

        return $result;
    }

    function getData($param)
    {

        $result = DB::selectOne(
            'SELECT warehouseid,warehousename,address,city,contactperson,phone,fax,upddate,upduser from inmswarehouse WHERE warehouseid = :warehouseid',
            [
                'warehouseid' => $param['warehouseid']
            ]
        );

        return $result;
    }
}

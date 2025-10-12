<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class GroupRekening extends BaseModel
{
    use HasFactory;

    protected $table = 'cfmsgrouprek';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::select(
            "SELECT grouprekid as group_rek_id,grouprekname as group_rek_name,
            case when fgtipe=1 then 'AKTIVA LANCAR'
                when fgtipe=2 then 'KEWAJIBAN'
                when fgtipe=3 then 'MODAL'
                when fgtipe=4 then 'PENDAPATAN'
                when fgtipe=5 then 'HPP'
                when fgtipe=6 then 'BEBAN OPERASIONAL'
                when fgtipe=7 then 'BEBAN LAINNYA'
                when fgtipe=8 then 'PEND LAINNYA'
                when fgtipe=9 then 'AKTIVA TETAP' END AS jenis
            from CFMsGroupRek 
            WHERE grouprekname LIKE :search_keyword 
            order by grouprekid",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'

            ]
        );

        return $result;
    }

    function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from cfmsgrouprek WHERE grouprekid = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

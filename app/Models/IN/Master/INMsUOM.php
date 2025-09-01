<?php

namespace App\Models\IN\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class INMsUOM extends Model
{
    use HasFactory;

    protected $table = 'inmsuom';

    public $timestamps = false;

    function getListData($param)
    {
        $result = DB::select(
            'SELECT uomid from inmsuom where uomid like :uomidkeyword order by uomid',
            [
                'uomidkeyword' => '%' . $param['uomidkeyword'] . '%'
            ]
            
        );

        return $result;
    }

    function cekSatuan($uomid)
    {

        $result = DB::selectOne(
            'SELECT * from inmsuom WHERE uomid = :uomid',
            [
                'uomid' => $uomid
            ]
        );

        return $result;
    }
}
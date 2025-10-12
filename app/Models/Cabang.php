<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Cabang extends BaseModel
{
    use HasFactory;

    protected $table = 'mscabang';

    public $timestamps = false;

    function getAllData()
    {
        $result = DB::select(
            "SELECT company_id,company_code,company_name,company_address from mscabang
            order by company_code "
        );

        return $result;
    }

    function cekData($id)
    {

        $result = DB::selectOne(
            'SELECT * from mscabang WHERE company_id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

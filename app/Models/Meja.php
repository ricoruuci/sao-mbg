<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Meja extends BaseModel
{
    use HasFactory;

    protected $table = 'msmeja';

    public $timestamps = false;

    function getAllData()
    {
        $result = DB::select(
            "SELECT kdmeja as nomor_meja from msmeja
            order by kdmeja"
        );

        return $result;
    }

    function cekData($id)
    {

        $result = DB::selectOne(
            'SELECT * from msmeja WHERE kdmeja = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

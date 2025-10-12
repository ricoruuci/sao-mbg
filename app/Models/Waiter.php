<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class Waiter extends BaseModel
{
    use HasFactory;

    protected $table = 'mswaiter';

    public $timestamps = false;

    function getAllData()
    {
        $result = DB::select(
            "SELECT waiter,upddate,upduser from mswaiter order by waiter"
        );

        return $result;
    }

    function cekData($id)
    {

        $result = DB::selectOne(
            'SELECT * from mswaiter WHERE waiter = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

}

?>

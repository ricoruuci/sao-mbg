<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use function PHPUnit\Framework\isNull;

class SetRekening extends BaseModel
{
    use HasFactory;

    protected $table = 'setrekening';

    public $timestamps = false;

    function getAllData($params)
    {
        $result = DB::selectOne(
            "SELECT 
                a.drpb as rek_pembelian,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drpb) as rek_name_pembelian,
                a.drtaxpb as rek_ppn_beli,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drtaxpb) as rek_name_ppn_beli,
                a.drap as rek_hutang,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drap) as rek_name_hutang,
                a.drpj as rek_penjualan,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drpj) as rek_name_penjualan,
                a.drtaxpj as rek_ppn_jual,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drtaxpj) as rek_name_ppn_jual,
                a.drar as rek_piutang,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drar) as rek_name_piutang,
                a.drkas as rek_kas,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drkas) as rek_name_kas,
                a.drlaba as rek_laba,
                (select x.rekeningname from cfmsrekening x where x.rekeningid=a.drlaba) as rek_name_laba
                from setrekening a ");

        return $result;
    }

    function updateData($param)
    {
        $result = DB::update(
            "UPDATE setrekening SET 
            drpb = :drpb,
            drtaxpb = :drtaxpb,
            drap = :drap,
            drpj = :drpj,
            drtaxpj = :drtaxpj,
            drar = :drar,
            drkas = :drkas,
            drlaba = :drlaba ",
            [
                'drpb' => $param['rek_pembelian'],
                'drtaxpb' => $param['rek_ppn_beli'],
                'drap' => $param['rek_hutang'],
                'drpj' => $param['rek_penjualan'],
                'drtaxpj' => $param['rek_ppn_jual'],
                'drar' => $param['rek_piutang'],
                'drkas' => $param['rek_kas'],
                'drlaba' => $param['rek_laba'],
            ]
        );

        return $result;
    }

}

?>

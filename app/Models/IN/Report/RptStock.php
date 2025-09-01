<?php

namespace App\Models\IN\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class RptStock extends Model
{
    function laporanStock($param)
    {

        $result = DB::select(
        "SELECT * from (
        select a.itemid, a.itemname, a.uomid, b.groupdesc, c.productdesc,
        isnull((select sum(case when x.fgtrans<50  then x.qty else x.qty*-1 end) from allitem x where convert(varchar(8),x.transdate,112)<=:tanggal
        and x.itemid=a.itemid and x.warehouseid=:warehouseid),0) as stock
        
        from inmsitem a 
        inner join inmsgroup b on a.groupid=b.groupid
        inner join inmsproduct c on a.productid=c.productid
        where a.fgactive='y'
        
        ) as k where k.stock<>0
        order by k.itemname ",
            [
                'tanggal' => $param['tanggal'],
                'warehouseid' => $param['warehouseid']
            ]
        );

        return $result;

    }



}

?>
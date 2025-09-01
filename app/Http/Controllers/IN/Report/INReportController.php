<?php

namespace App\Http\Controllers\IN\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IN\Report\RptStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class INReportController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getRptStock(Request $request)
    {
        $laporan = new RptStock();

        $result = $laporan->laporanStock(
            [
                'tanggal' => $request->input('tanggal'),
                'warehouseid' => $request->input('warehouseid')
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>
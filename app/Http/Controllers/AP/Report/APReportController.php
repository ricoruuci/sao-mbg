<?php

namespace App\Http\Controllers\AP\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AP\Report\RptHutang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class APReportController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getRptHutang(Request $request)
    {
        $laporan = new RptHutang();

        $result = $laporan->laporanHutang(
            [
                'tanggal' => $request->input('tanggal'),
                'fglunas' => $request->input('fglunas')
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>
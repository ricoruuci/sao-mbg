<?php

namespace App\Http\Controllers\AR\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AR\Report\RptPiutang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class ARReportController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getRptPiutang(Request $request)
    {
        $laporan = new RptPiutang();

        $result = $laporan->laporanPiutang(
            [
                'tanggal' => $request->input('tanggal'),
                'fglunas' => $request->input('fglunas')
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getRptPenjualan(Request $request)
    {
        $laporan = new RptPiutang();

        $result = $laporan->laporanRekapPenjualan(
            [
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'fglunas' => $request->input('fglunas')
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>
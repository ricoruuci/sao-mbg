<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getGrafikPenjualan()
    {
        $laporan = new Dashboard();

        $result = $laporan->getListData();


        return $this->responseData($result);
    }

    public function getSalesYear()
    {
        $laporan = new Dashboard();


        $header = $laporan->gettahun();
        $detail = $laporan->getRekapJualTahunan();

        $result = [
            'data' => $header,
            'total' => $detail
        ];

        return $result;
    }

    public function getTotalPO()
    {
        $laporan = new Dashboard();

        $result = $laporan->getTotalPO();


        return $this->responseData($result);
    }

    public function getTotalSO()
    {
        $laporan = new Dashboard();

        $result = $laporan->getTotalSO();


        return $this->responseData($result);
    }

    public function getTotalJual()
    {
        $laporan = new Dashboard();

        $result = $laporan->getTotalJual();


        return $this->responseData($result);
    }

    public function getTotalBeli()
    {
        $laporan = new Dashboard();

        $result = $laporan->getTotalBeli();


        return $this->responseData($result);
    }

    public function getSoPending()
    {
        $laporan = new Dashboard();

        $result = $laporan->getTotalSOPending();


        return $this->responseData($result);
    }

    public function getUserAktif()
    {
        $laporan = new Dashboard();

        $result = $laporan->getUserAktif();


        return $this->responseData($result);
    }
}

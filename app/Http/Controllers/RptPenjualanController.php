<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RptPenjualan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RptPenjualan\GetLapPenjualanRequest;
use App\Http\Requests\RptPenjualan\GetLapPenjualanHarianRequest;

class RptPenjualanController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getLapPenjualan(GetLapPenjualanRequest $request)
    {
        $model = new RptPenjualan();

        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getLapPenjualan([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', '')
            ]);
        }
        else
        {
            $result = $model->getLapPenjualan([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', ''),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getLapPenjualanHarian(GetLapPenjualanHarianRequest $request)
    {
        $model = new RptPenjualan();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getLapPenjualanHarian([
                'transdate' => $request->input('transdate')
            ]);
        }
        else
        {
            $result = $model->getLapPenjualanHarian([
                'transdate' => $request->input('transdate'),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }

        return $this->responseData($result);
    }

}

?>

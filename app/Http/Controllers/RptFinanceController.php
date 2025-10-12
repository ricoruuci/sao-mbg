<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RptFinance;
use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RptFinance\GetRequestBukuBesar;
use App\Http\Requests\RptFinance\GetRequestLabaRugi;
use App\Http\Requests\RptFinance\GetRequestNeraca;

class RptFinanceController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getRptBukuBesar(GetRequestBukuBesar $request)
    {
        $model = new RptFinance();
        $rekening = new Rekening();

        $rekeningId = $request->input('rekening_id');

        if ($rekeningId !== null && $rekeningId !== '') {
            $cek = $rekening->cekData($rekeningId);
            if ($cek == false) {
                return $this->responseError('Rekening not found', 404);
            }
        }

        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getRptBukuBesar([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'rekening_id' => $request->input('rekening_id', '')
            ]);
        }
        else
        {
            $result = $model->getRptBukuBesar([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'rekening_id' => $request->input('rekening_id', ''),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }

        return $this->responseData($result);
    }

    public function getRptLabaRugi(GetRequestLabaRugi $request)
    {
        $model = new RptFinance();

        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getRptLabaRugi([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai')
            ]);
        }
        else
        {
            $result = $model->getRptLabaRugi([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]); 
        }

        return $this->responseData($result);
    }

    public function getRptNeraca(GetRequestNeraca $request)
    {
        $model = new RptFinance();

        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='ADM')
        {
            $result = $model->getRptNeraca([
                'periode' => $request->input('periode')
            ]);
        }
        else
        {
            $result = $model->getRptNeraca([
                'periode' => $request->input('periode'),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]); 
        }

        return $this->responseData($result);
    }

}

?>

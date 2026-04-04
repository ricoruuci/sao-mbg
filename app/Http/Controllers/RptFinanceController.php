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

        $rekeningId = trim((string) $request->input('rekening_id', ''));

        if ($rekeningId !== '') {
            $cek = $rekening->cekData($rekeningId);
            if ($cek == false) {
                return $this->responseError('Rekening not found', 404);
            }

            $rekeningFields = is_object($cek) ? get_object_vars($cek) : [];
            foreach ($rekeningFields as $key => $value) {
                if (strtolower((string) $key) === 'rekeningid') {
                    $rekeningId = trim((string) $value);
                    break;
                }
            }
        }

        $result = $model->getRptBukuBesar([
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
            'rekening_id' => $rekeningId,
        ]);

        return $this->responseData($result);
    }

    public function getRptLabaRugi(GetRequestLabaRugi $request)
    {
        $model = new RptFinance();

        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='USR')
        {
            $result = $model->getRptLabaRugi([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }
        else
        {
            $result = $model->getRptLabaRugi([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'company_id' => $request->input('company_id', Auth::user()->currentAccessToken()['company_id'])
            ]);
        }

        return $this->responseData($result);
    }

    public function getRptNeraca(GetRequestNeraca $request)
    {
        $model = new RptFinance();

        $result = $model->getRptNeraca([
            'periode' => $request->input('periode'),
        ]);

        return $this->responseData($result);
    }

}

?>

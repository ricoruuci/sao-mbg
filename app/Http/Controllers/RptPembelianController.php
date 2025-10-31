<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RptPembelian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RptPembelian\GetLapPembelianRequest;
use App\Http\Requests\RptPembelian\GetLapHutangRequest;
use App\Http\Requests\RptPembelian\GetLapBeliAdjustmentRequest;
use App\Http\Requests\RptPembelian\UpdateNotaBeliRequest;

class RptPembelianController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getLapPembelian(GetLapPembelianRequest $request)
    {
        $model = new RptPembelian();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='USR')
        {
            $result = $model->getLapPembelian([
                'dari' => $request->input('dari'),
                'sampai' => $request->input('sampai'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', ''),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }
        else
            {
                // dd(var_dump($request->company_id));
                $result = $model->getLapPembelian([
                    'dari' => $request->input('dari'),
                    'sampai' => $request->input('sampai'),
                    'search_keyword' => $request->input('search_keyword', ''),
                    'supplier_keyword' => $request->input('supplier_keyword', ''),
                    'company_id' => $request->input('company_id', Auth::user()->currentAccessToken()['company_id'])
                ]);
            }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getLapHutang(GetLapHutangRequest $request)
    {
        $model = new RptPembelian();
        $user = new User();
        $cek = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($cek->kdjabatan=='USR')
        {
            $result = $model->getLapHutang([
                'transdate' => $request->input('transdate'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', ''),
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);
        }
        else
        {
            $result = $model->getLapHutang([
                'transdate' => $request->input('transdate'),
                'search_keyword' => $request->input('search_keyword', ''),
                'supplier_keyword' => $request->input('supplier_keyword', ''),
                'company_id' => $request->input('company_id', Auth::user()->currentAccessToken()['company_id'])
            ]);
        }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getLaporanBeliAdjustment(GetLapBeliAdjustmentRequest $request)
    {
        $model = new RptPembelian();

        $data = $model->getLaporanBeliAdjustment([
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
            'adjustment' => $request->input('adjustment') ?? 0,
            'company_id' => $request->input('company_id') ?? Auth::user()->currentAccessToken()['company_id']
        ]);

        $grandtotal = 0;
        $grandtotaladjustment = 0;

        foreach ($data as $res) {
            $grandtotal += $res->total;
            $grandtotaladjustment += $res->total_adjustment;
        }

        $result = [
            'data' => $data,
            'summary' => [
                'grand_total' => $grandtotal,
                'grand_total_adjustment' => $grandtotaladjustment
            ]
        ];

        return $result;
    }

    public function updateFgUpload(UpdateNotaBeliRequest $request)
    {
        $model = new RptPembelian();

        $params = $request->input('data');

        DB::beginTransaction();

        try
        {
            foreach ($params as $param) {
                $result = $model->updateFgUploadNota([
                    'adjustment' => $request->input('adjustment'),
                    'nota_beli' => $param['nota_beli'],
                ]);

                if ($result == false) {
                    return $this->responseError('Gagal menyimpan data rekening', 500);
                }
            }

            DB::commit();
            return $this->responseSuccess('Berhasil Menyimpan Data', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>

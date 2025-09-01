<?php

namespace App\Http\Controllers\AP\Activity;

use App\Http\Controllers\Controller;
use App\Models\AP\Activity\OtorisasiPembelian;
use Illuminate\Http\Request;
use App\Models\AP\Activity\PurchaseOrderHd;
use App\Models\AP\Activity\PurchaseOrderDt;
use App\Models\AP\Master\APMsSupplier;
use App\Models\AR\Master\ARMsSales;
use App\Models\IN\Master\INMsItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class OtorisasiPembelianController extends Controller
{
    use ArrayPaginator, HttpResponse;


    public function getListOto(Request $request)
    {
        $otorisasi = new OtorisasiPembelian();

        $result = $otorisasi->getHeader([
            'purchaseidkeyword' => $request->input('purchaseidkeyword') ?? '',
            'suppnamekeyword' => $request->input('suppnamekeyword') ?? ''
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function updateData(Request $request)
    {
        $otorisasi = new OtorisasiPembelian();

        $cek = $otorisasi->cekPurchase($request->input('purchaseid'));

        if ($cek == false) {

            return $this->responseError('nota purchase tidak terdaftar', 400);
        }

        DB::beginTransaction();

        try {
            $update = $otorisasi->updateData([
                'purchaseid' => $request->input('purchaseid')
            ]);

            DB::commit();

            return $this->responseSuccess('otorisasi berhasil', 200, ['nota purchase :' => $request->input('purchaseid')]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}

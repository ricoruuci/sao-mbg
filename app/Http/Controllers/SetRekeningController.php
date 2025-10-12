<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SetRekening;
use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\SetRekening\UpdateRequest;
use App\Http\Requests\SetRekening\GetRequest;
use Illuminate\Support\Facades\Auth;

class SetRekeningController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function updateData(UpdateRequest $request)
    {
        $model = new SetRekening();

        $model_rekening = new Rekening();

        $params = [
            'rek_pembelian' => $request->rek_pembelian,
            'rek_penjualan' => $request->rek_penjualan,
            'rek_ppn_beli' => $request->rek_ppn_beli,
            'rek_ppn_jual' => $request->rek_ppn_jual,
            'rek_hutang' => $request->rek_hutang,
            'rek_piutang' => $request->rek_piutang,
            'rek_kas' => $request->rek_kas,
            'rek_laba' => $request->rek_laba,
        ];

        $cek = $model_rekening->cekData($request->rek_pembelian);
        if ($cek == false) {
            return $this->responseError('Rekening Pembelian tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_penjualan);
        if ($cek == false) {
            return $this->responseError('Rekening Penjualan tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_ppn_beli);
        if ($cek == false) {
            return $this->responseError('Rekening PPN Pembelian tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_ppn_jual);
        if ($cek == false) {
            return $this->responseError('Rekening PPN Penjualan tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_hutang);
        if ($cek == false) {
            return $this->responseError('Rekening Hutang tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_piutang);
        if ($cek == false) {
            return $this->responseError('Rekening Piutang tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_kas);
        if ($cek == false) {
            return $this->responseError('Rekening Kas tidak ada atau tidak ditemukan', 404);
        }
        $cek = $model_rekening->cekData($request->rek_laba);
        if ($cek == false) {
            return $this->responseError('Rekening Laba tidak ada atau tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $updateResult = $model->updateData($params);

            if ($updateResult == false) {
                return $this->responseError('Gagal memperbarui data Set Rekening', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data Set Rekening berhasil diperbarui', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
    
    public function getListData(GetRequest $request)
    {
        $group_model = new SetRekening();

        $result = $group_model->getAllData($request->all());

        return $this->responseData($result);

    }

}

?>


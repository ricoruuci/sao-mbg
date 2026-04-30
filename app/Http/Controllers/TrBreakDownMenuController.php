<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TrBreakDownMenuHd;
use App\Models\TrBreakDownMenuDt;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;

class TrBreakDownMenuController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getDataById(Request $request)
    {
        $modelHeader = new TrBreakDownMenuHd();
        $modelDetail = new TrBreakDownMenuDt();
        $id = $request->trbreakdownhd_code;
        $cek = $modelHeader->getDataById($id);
        if (!$cek) {
            return $this->responseError('Data tidak ditemukan', 404);
        }
        $header = $modelHeader->getDataById($id);
        $detail = $modelDetail->getAllData($id);
        $dataBahanBaku = $modelDetail->getDataBahanBaku($id);
        return $this->responseData([
            'header' => $header ?? [],
            'detail' => $detail ?? [],
            'dataBahanBaku' => $dataBahanBaku ?? [],
        ]);
    }

    public function insertData(Request $request)
    {
        $modelHeader = new TrBreakDownMenuHd();
        $modelDetail = new TrBreakDownMenuDt();
        DB::beginTransaction();
        try {
            $params = [
                // trbreakdownhd_code tidak perlu dari request, akan otomatis
                'trbreakdownhd_date' => $request->trbreakdownhd_date,
                'trbreakdownhd_qty_beneficiaries' => $request->trbreakdownhd_qty_beneficiaries,
                'trbreakdownhd_note' => $request->trbreakdownhd_note,
                'trbreakdownhd_company_id' => $request->trbreakdownhd_company_id,
                'trbreakdownhd_company_name' => $request->trbreakdownhd_company_name,
                'upduser' => Auth::user()->currentAccessToken()['namauser'] ?? 'system',
            ];
            $kodeHeader = $modelHeader->insertData($params);
            if (!$kodeHeader) {
                DB::rollBack();
                return $this->responseError('Gagal menyimpan header', 500);
            }
            $arrDetail = $request->input('detail');
            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('Detail tidak boleh kosong', 400);
            }
            foreach ($arrDetail as $item) {
                $insertDetail = $modelDetail->insertData([
                    'trbreakdownmenudt_hd_code' => $kodeHeader,
                    'trbreakdownmenudt_itemid' => $item['trbreakdownmenudt_itemid'] ?? null,
                    'trbreakdownmenudt_qty' => $item['trbreakdownmenudt_qty'] ?? 0,
                    'trbreakdownmenudt_uomid' => $item['trbreakdownmenudt_uomid'] ?? null,
                    'trbreakdownmenudt_note' => $item['trbreakdownmenudt_note'] ?? null,
                ]);
                if (!$insertDetail) {
                    DB::rollBack();
                    return $this->responseError('Gagal menyimpan detail', 500);
                }
            }

            $dataBahanBaku = $modelDetail->getDataBahanBaku($kodeHeader);

            DB::commit();
            return $this->responseSuccess('Data berhasil disimpan', 200, [
                'trbreakdownhd_code' => $kodeHeader,
                'dataBahanBaku' => $dataBahanBaku ?? [],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(Request $request)
    {
        $modelHeader = new TrBreakDownMenuHd();
        $modelDetail = new TrBreakDownMenuDt();
        $cek = $modelHeader->getDataById($request->trbreakdownhd_code);
        if (!$cek) {
            return $this->responseError('Data tidak ditemukan', 404);
        }
        DB::beginTransaction();
        try {
            $params = [
                'trbreakdownhd_code' => $request->trbreakdownhd_code,
                'trbreakdownhd_date' => $request->trbreakdownhd_date,
                'trbreakdownhd_qty_beneficiaries' => $request->trbreakdownhd_qty_beneficiaries,
                'trbreakdownhd_note' => $request->trbreakdownhd_note,
                'trbreakdownhd_company_id' => $request->trbreakdownhd_company_id,
                'trbreakdownhd_company_name' => $request->trbreakdownhd_company_name,
                'upduser' => Auth::user()->currentAccessToken()['namauser'] ?? 'system',
            ];
            $updateResult = $modelHeader->updateData($params);
            if (!$updateResult) {
                DB::rollBack();
                return $this->responseError('Gagal update header', 500);
            }
            $modelDetail->deleteByHdCode($request->trbreakdownhd_code);
            $arrDetail = $request->input('detail');
            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('Detail tidak boleh kosong', 400);
            }
            foreach ($arrDetail as $item) {
                $insertDetail = $modelDetail->insertData([
                    'trbreakdownmenudt_hd_code' => $request->trbreakdownhd_code,
                    'trbreakdownmenudt_itemid' => $item['trbreakdownmenudt_itemid'] ?? null,
                    'trbreakdownmenudt_qty' => $item['trbreakdownmenudt_qty'] ?? 0,
                    'trbreakdownmenudt_uomid' => $item['trbreakdownmenudt_uomid'] ?? null,
                    'trbreakdownmenudt_note' => $item['trbreakdownmenudt_note'] ?? null,
                ]);
                if (!$insertDetail) {
                    DB::rollBack();
                    return $this->responseError('Gagal update detail', 500);
                }
            }

            $dataBahanBaku = $modelDetail->getDataBahanBaku($request->trbreakdownhd_code);
            DB::commit();
            return $this->responseSuccess('Data berhasil diupdate', 200, [
                'trbreakdownhd_code' => $request->trbreakdownhd_code,
                'dataBahanBaku' => $dataBahanBaku ?? [],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(Request $request)
    {
        $modelHeader = new TrBreakDownMenuHd();
        $modelDetail = new TrBreakDownMenuDt();
        $id = $request->trbreakdownhd_code;
        $cek = $modelHeader->getDataById($id);
        if (!$cek) {
            return $this->responseError('Data tidak ditemukan', 404);
        }
        DB::beginTransaction();
        try {
            $modelDetail->deleteByHdCode($id);
            $deleteResult = $modelHeader->deleteData($id);
            if (!$deleteResult) {
                DB::rollBack();
                return $this->responseError('Gagal hapus data', 500);
            }
            DB::commit();
            return $this->responseSuccess('Data berhasil dihapus', 200, [
                'trbreakdownhd_code' => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function getListData(Request $request)
    {
        $model = new TrBreakDownMenuHd();
        $params = [
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'search_keyword' => $request->search_keyword ?? '',
        ];
        $result = $model->getAllData($params);
        $resultPaginated = $this->arrayPaginator($request, $result);
        return $this->responsePagination($resultPaginated);
    }
}

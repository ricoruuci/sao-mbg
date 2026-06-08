<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderAtkDt;
use App\Models\PurchaseOrderAtkHd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\PurchaseOrderAtk\InsertRequest;
use App\Http\Requests\PurchaseOrderAtk\UpdateRequest;
use App\Http\Requests\PurchaseOrderAtk\DeleteRequest;
use App\Http\Requests\PurchaseOrderAtk\GetRequestById;
use App\Http\Requests\PurchaseOrderAtk\GetRequest;

class PurchaseOrderAtkController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $modelHeader = new PurchaseOrderAtkHd();
        $modelDetail = new PurchaseOrderAtkDt();

        $supplier = $modelHeader->getSupplierDataById($request->purchase_order_atk_supplier_id);

        if ($supplier == false) {
            return $this->responseError('supplier tidak ditemukan', 404);
        }

        $params = [
            'purchase_order_atk_date' => $request->purchase_order_atk_date,
            'purchase_order_atk_date_costing' => $request->purchase_order_atk_date_costing,
            'purchase_order_atk_supplier_id' => $request->purchase_order_atk_supplier_id,
            'purchase_order_atk_supplier_name' => $supplier->supplier_name,
            'purchase_order_atk_pic_name' => $supplier->supplier_pic_name,
            'purchase_order_atk_pic_phone' => $supplier->supplier_pic_phone,
            'purchase_order_atk_to' => $request->purchase_order_atk_to,
            'purchase_order_atk_address' => $request->purchase_order_atk_address,
            'purchase_order_atk_note' => $request->purchase_order_atk_note ?? '',
            'purchase_order_atk_discount' => $request->purchase_order_atk_discount ?? 0,
            'purchase_order_atk_tax' => $request->purchase_order_atk_tax ?? 0,
            'purchase_order_atk_koefisien' => $request->purchase_order_atk_koefisien ?? 0,
            'purchase_order_atk_budget' => $request->purchase_order_atk_budget ?? 0,
            'purchase_order_atk_budget_over' => $request->purchase_order_atk_budget_over ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $purchaseOrderAtkId = $modelHeader->generatePurchaseOrderAtkId(
                $request->purchase_order_atk_date_costing,
                $supplier->supplier_name,
                $request->purchase_order_atk_to
            );

            $params['purchase_order_atk_id'] = $purchaseOrderAtkId;

            $insertHeader = $modelHeader->insertData($params);

            if ($insertHeader == false) {
                DB::rollBack();
                return $this->responseError('insert header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('detail tidak boleh kosong', 400);
            }

            foreach ($arrDetail as $detail) {
                $detailTotal = ($detail['purchase_order_atk_detail_qty_invoice'] ?? 0) * $detail['purchase_order_atk_detail_price'];

                $insertDetail = $modelDetail->insertData([
                    'purchase_order_atk_id' => $purchaseOrderAtkId,
                    'purchase_order_atk_detail_itemid' => $detail['purchase_order_atk_detail_itemid'],
                    'purchase_order_atk_detail_itemname' => $detail['purchase_order_atk_detail_itemname'],
                    'purchase_order_atk_detail_formula' => $detail['purchase_order_atk_detail_formula'] ?? 0,
                    'purchase_order_atk_detail_qty' => $detail['purchase_order_atk_detail_qty'],
                    'purchase_order_atk_detail_qty_invoice' => $detail['purchase_order_atk_detail_qty_invoice'] ?? 0,
                    'purchase_order_atk_detail_uom' => $detail['purchase_order_atk_detail_uom'],
                    'purchase_order_atk_detail_last_price' => $detail['purchase_order_atk_detail_last_price'] ?? 0,
                    'purchase_order_atk_detail_price' => $detail['purchase_order_atk_detail_price'],
                    'purchase_order_atk_detail_total' => $detailTotal,
                    'purchase_order_atk_detail_send_date' => $detail['purchase_order_atk_detail_send_date'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($insertDetail == false) {
                    DB::rollBack();
                    return $this->responseError('insert detail gagal', 400);
                }
            }

            $hitung = $modelHeader->hitungTotal($purchaseOrderAtkId);

            if ($hitung) {
                $modelHeader->updateTotal([
                    'purchase_order_atk_subtotal' => (float) $hitung->purchase_order_atk_subtotal,
                    'purchase_order_atk_tax_amount' => (float) $hitung->purchase_order_atk_tax_amount,
                    'purchase_order_atk_grandtotal' => (float) $hitung->purchase_order_atk_grandtotal,
                    'purchase_order_atk_id' => $purchaseOrderAtkId,
                ]);
            }

            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, [
                'purchase_order_atk_id' => $purchaseOrderAtkId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $modelHeader = new PurchaseOrderAtkHd();
        $modelDetail = new PurchaseOrderAtkDt();

        $supplier = $modelHeader->getSupplierDataById($request->purchase_order_atk_supplier_id);

        if ($supplier == false) {
            return $this->responseError('supplier tidak ditemukan', 404);
        }

        $cek = $modelHeader->cekData($request->purchase_order_atk_id ?? '');

        if ($cek == false) {
            return $this->responseError('purchase order atk tidak ada atau tidak ditemukan', 400);
        }

        $params = [
            'purchase_order_atk_id' => $request->purchase_order_atk_id,
            'purchase_order_atk_date' => $request->purchase_order_atk_date,
            'purchase_order_atk_date_costing' => $request->purchase_order_atk_date_costing,
            'purchase_order_atk_supplier_id' => $request->purchase_order_atk_supplier_id,
            'purchase_order_atk_supplier_name' => $supplier->supplier_name,
            'purchase_order_atk_pic_name' => $supplier->supplier_pic_name,
            'purchase_order_atk_pic_phone' => $supplier->supplier_pic_phone,
            'purchase_order_atk_to' => $request->purchase_order_atk_to,
            'purchase_order_atk_address' => $request->purchase_order_atk_address,
            'purchase_order_atk_note' => $request->purchase_order_atk_note ?? '',
            'purchase_order_atk_discount' => $request->purchase_order_atk_discount ?? 0,
            'purchase_order_atk_tax' => $request->purchase_order_atk_tax ?? 0,
            'purchase_order_atk_koefisien' => $request->purchase_order_atk_koefisien ?? 0,
            'purchase_order_atk_budget' => $request->purchase_order_atk_budget ?? 0,
            'purchase_order_atk_budget_over' => $request->purchase_order_atk_budget_over ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $oldId = $request->purchase_order_atk_id;

            // Regenerate ID berdasarkan _to dan supplier baru
            $newId = $modelHeader->generatePurchaseOrderAtkId(
                $request->purchase_order_atk_date_costing,
                $supplier->supplier_name,
                $request->purchase_order_atk_to
            );

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('detail tidak boleh kosong', 400);
            }

            // 1. Hapus detail lama dulu (lepas referensi FK ke oldId)
            $modelDetail->deleteData($oldId);

            // 2. Jika ID berubah, update PK header (aman karena detail sudah dihapus)
            if ($oldId !== $newId) {
                $modelHeader->updateId($oldId, $newId);
                $params['purchase_order_atk_id'] = $newId;
            }

            // 3. Update field-field header
            $updateHeader = $modelHeader->updateData($params);

            if ($updateHeader === false) {
                DB::rollBack();
                return $this->responseError('update header gagal', 400);
            }

            // 4. Insert ulang detail dengan newId
            foreach ($arrDetail as $detail) {
                $detailTotal = ($detail['purchase_order_atk_detail_qty_invoice'] ?? 0) * $detail['purchase_order_atk_detail_price'];

                $insertDetail = $modelDetail->insertData([
                    'purchase_order_atk_id' => $newId,
                    'purchase_order_atk_detail_itemid' => $detail['purchase_order_atk_detail_itemid'],
                    'purchase_order_atk_detail_itemname' => $detail['purchase_order_atk_detail_itemname'],
                    'purchase_order_atk_detail_formula' => $detail['purchase_order_atk_detail_formula'] ?? 0,
                    'purchase_order_atk_detail_qty' => $detail['purchase_order_atk_detail_qty'],
                    'purchase_order_atk_detail_qty_invoice' => $detail['purchase_order_atk_detail_qty_invoice'] ?? 0,
                    'purchase_order_atk_detail_uom' => $detail['purchase_order_atk_detail_uom'],
                    'purchase_order_atk_detail_last_price' => $detail['purchase_order_atk_detail_last_price'] ?? 0,
                    'purchase_order_atk_detail_price' => $detail['purchase_order_atk_detail_price'],
                    'purchase_order_atk_detail_total' => $detailTotal,
                    'purchase_order_atk_detail_send_date' => $detail['purchase_order_atk_detail_send_date'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($insertDetail == false) {
                    DB::rollBack();
                    return $this->responseError('update detail gagal', 400);
                }
            }

            $hitung = $modelHeader->hitungTotal($newId);

            if ($hitung) {
                $modelHeader->updateTotal([
                    'purchase_order_atk_subtotal' => (float) $hitung->purchase_order_atk_subtotal,
                    'purchase_order_atk_tax_amount' => (float) $hitung->purchase_order_atk_tax_amount,
                    'purchase_order_atk_grandtotal' => (float) $hitung->purchase_order_atk_grandtotal,
                    'purchase_order_atk_id' => $newId,
                ]);
            }

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, [
                'purchase_order_atk_id' => $newId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(GetRequest $request)
    {
        $model = new PurchaseOrderAtkHd();

        $result = $model->getAllData([
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'search_keyword' => $request->search_keyword,
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getDataById(GetRequestById $request)
    {
        $modelHeader = new PurchaseOrderAtkHd();
        $modelDetail = new PurchaseOrderAtkDt();

        $result = $modelHeader->getDataById($request->purchase_order_atk_id ?? '');

        if ($result) {
            $header = $result;
            $detailResult = $modelDetail->getDataById($result->purchase_order_atk_id ?? '');
            $detail = !empty($detailResult) ? $detailResult : [];
        } else {
            $header = [];
            $detail = [];
        }

        return $this->responseData([
            'header' => $header,
            'detail' => $detail,
        ]);
    }

    public function deleteData(DeleteRequest $request)
    {
        $modelHeader = new PurchaseOrderAtkHd();
        $modelDetail = new PurchaseOrderAtkDt();

        $id = $request->purchase_order_atk_id;

        $cek = $modelHeader->cekData($id);
        if ($cek == false) {
            return $this->responseError('Purchase Order Atk tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $modelDetail->deleteData($id);

            $deleteResult = $modelHeader->deleteData($id);

            if ($deleteResult == false) {
                DB::rollBack();
                return $this->responseError('Gagal menghapus data Purchase Order Atk', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data Purchase Order Atk berhasil dihapus', 200, [
                'purchase_order_atk_id' => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}

?>

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderKitchenDt;
use App\Models\PurchaseOrderKitchenHd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\PurchaseOrderKitchen\InsertRequest;
use App\Http\Requests\PurchaseOrderKitchen\UpdateRequest;
use App\Http\Requests\PurchaseOrderKitchen\DeleteRequest;
use App\Http\Requests\PurchaseOrderKitchen\GetRequestById;
use App\Http\Requests\PurchaseOrderKitchen\GetRequest;

class PurchaseOrderKitchenController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $modelHeader = new PurchaseOrderKitchenHd();
        $modelDetail = new PurchaseOrderKitchenDt();

        $supplier = $modelHeader->getSupplierDataById($request->purchase_order_kitchen_supplier_id);

        if ($supplier == false) {
            return $this->responseError('supplier tidak ditemukan', 404);
        }

        $params = [
            'purchase_order_kitchen_date' => $request->purchase_order_kitchen_date,
            'purchase_order_kitchen_date_costing' => $request->purchase_order_kitchen_date_costing,
            'purchase_order_kitchen_supplier_id' => $request->purchase_order_kitchen_supplier_id,
            'purchase_order_kitchen_supplier_name' => $supplier->supplier_name,
            'purchase_order_kitchen_pic_name' => $supplier->supplier_pic_name,
            'purchase_order_kitchen_pic_phone' => $supplier->supplier_pic_phone,
            'purchase_order_kitchen_to' => $request->purchase_order_kitchen_to,
            'purchase_order_kitchen_address' => $request->purchase_order_kitchen_address,
            'purchase_order_kitchen_note' => $request->purchase_order_kitchen_note ?? '',
            'purchase_order_kitchen_discount' => $request->purchase_order_kitchen_discount ?? 0,
            'purchase_order_kitchen_tax' => $request->purchase_order_kitchen_tax ?? 0,
            'purchase_order_kitchen_koefisien' => $request->purchase_order_kitchen_koefisien ?? 0,
            'purchase_order_kitchen_budget' => $request->purchase_order_kitchen_budget ?? 0,
            'purchase_order_kitchen_budget_over' => $request->purchase_order_kitchen_budget_over ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $purchaseOrderKitchenId = $modelHeader->generatePurchaseOrderKitchenId(
                $request->purchase_order_kitchen_date,
                $supplier->supplier_name
            );

            $params['purchase_order_kitchen_id'] = $purchaseOrderKitchenId;

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
                $detailTotal = ($detail['purchase_order_kitchen_detail_qty_invoice'] ?? 0) * $detail['purchase_order_kitchen_detail_price'];

                $insertDetail = $modelDetail->insertData([
                    'purchase_order_kitchen_id' => $purchaseOrderKitchenId,
                    'purchase_order_kitchen_detail_itemid' => $detail['purchase_order_kitchen_detail_itemid'],
                    'purchase_order_kitchen_detail_itemname' => $detail['purchase_order_kitchen_detail_itemname'],
                    'purchase_order_kitchen_detail_formula' => $detail['purchase_order_kitchen_detail_formula'] ?? 0,
                    'purchase_order_kitchen_detail_qty' => $detail['purchase_order_kitchen_detail_qty'],
                    'purchase_order_kitchen_detail_qty_invoice' => $detail['purchase_order_kitchen_detail_qty_invoice'] ?? 0,
                    'purchase_order_kitchen_detail_uom' => $detail['purchase_order_kitchen_detail_uom'],
                    'purchase_order_kitchen_detail_last_price' => $detail['purchase_order_kitchen_detail_last_price'] ?? 0,
                    'purchase_order_kitchen_detail_price' => $detail['purchase_order_kitchen_detail_price'],
                    'purchase_order_kitchen_detail_total' => $detailTotal,
                    'purchase_order_kitchen_detail_send_date' => $detail['purchase_order_kitchen_detail_send_date'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($insertDetail == false) {
                    DB::rollBack();
                    return $this->responseError('insert detail gagal', 400);
                }
            }

            $hitung = $modelHeader->hitungTotal($purchaseOrderKitchenId);

            if ($hitung) {
                $modelHeader->updateTotal([
                    'purchase_order_kitchen_subtotal' => (float) $hitung->purchase_order_kitchen_subtotal,
                    'purchase_order_kitchen_tax_amount' => (float) $hitung->purchase_order_kitchen_tax_amount,
                    'purchase_order_kitchen_grandtotal' => (float) $hitung->purchase_order_kitchen_grandtotal,
                    'purchase_order_kitchen_id' => $purchaseOrderKitchenId,
                ]);
            }

            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, [
                'purchase_order_kitchen_id' => $purchaseOrderKitchenId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $modelHeader = new PurchaseOrderKitchenHd();
        $modelDetail = new PurchaseOrderKitchenDt();

        $supplier = $modelHeader->getSupplierDataById($request->purchase_order_kitchen_supplier_id);

        if ($supplier == false) {
            return $this->responseError('supplier tidak ditemukan', 404);
        }

        $cek = $modelHeader->cekData($request->purchase_order_kitchen_id ?? '');

        if ($cek == false) {
            return $this->responseError('purchase order kitchen tidak ada atau tidak ditemukan', 400);
        }

        $params = [
            'purchase_order_kitchen_id' => $request->purchase_order_kitchen_id,
            'purchase_order_kitchen_date' => $request->purchase_order_kitchen_date,
            'purchase_order_kitchen_date_costing' => $request->purchase_order_kitchen_date_costing,
            'purchase_order_kitchen_supplier_id' => $request->purchase_order_kitchen_supplier_id,
            'purchase_order_kitchen_supplier_name' => $supplier->supplier_name,
            'purchase_order_kitchen_pic_name' => $supplier->supplier_pic_name,
            'purchase_order_kitchen_pic_phone' => $supplier->supplier_pic_phone,
            'purchase_order_kitchen_to' => $request->purchase_order_kitchen_to,
            'purchase_order_kitchen_address' => $request->purchase_order_kitchen_address,
            'purchase_order_kitchen_note' => $request->purchase_order_kitchen_note ?? '',
            'purchase_order_kitchen_discount' => $request->purchase_order_kitchen_discount ?? 0,
            'purchase_order_kitchen_tax' => $request->purchase_order_kitchen_tax ?? 0,
            'purchase_order_kitchen_koefisien' => $request->purchase_order_kitchen_koefisien ?? 0,
            'purchase_order_kitchen_budget' => $request->purchase_order_kitchen_budget ?? 0,
            'purchase_order_kitchen_budget_over' => $request->purchase_order_kitchen_budget_over ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $updateHeader = $modelHeader->updateData($params);

            if ($updateHeader === false) {
                DB::rollBack();
                return $this->responseError('update header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('detail tidak boleh kosong', 400);
            }

            $modelDetail->deleteData($request->purchase_order_kitchen_id);

            foreach ($arrDetail as $detail) {
                $detailTotal = ($detail['purchase_order_kitchen_detail_qty_invoice'] ?? 0) * $detail['purchase_order_kitchen_detail_price'];

                $insertDetail = $modelDetail->insertData([
                    'purchase_order_kitchen_id' => $request->purchase_order_kitchen_id,
                    'purchase_order_kitchen_detail_itemid' => $detail['purchase_order_kitchen_detail_itemid'],
                    'purchase_order_kitchen_detail_itemname' => $detail['purchase_order_kitchen_detail_itemname'],
                    'purchase_order_kitchen_detail_formula' => $detail['purchase_order_kitchen_detail_formula'] ?? 0,
                    'purchase_order_kitchen_detail_qty' => $detail['purchase_order_kitchen_detail_qty'],
                    'purchase_order_kitchen_detail_qty_invoice' => $detail['purchase_order_kitchen_detail_qty_invoice'] ?? 0,
                    'purchase_order_kitchen_detail_uom' => $detail['purchase_order_kitchen_detail_uom'],
                    'purchase_order_kitchen_detail_last_price' => $detail['purchase_order_kitchen_detail_last_price'] ?? 0,
                    'purchase_order_kitchen_detail_price' => $detail['purchase_order_kitchen_detail_price'],
                    'purchase_order_kitchen_detail_total' => $detailTotal,
                    'purchase_order_kitchen_detail_send_date' => $detail['purchase_order_kitchen_detail_send_date'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($insertDetail == false) {
                    DB::rollBack();
                    return $this->responseError('update detail gagal', 400);
                }
            }

            $hitung = $modelHeader->hitungTotal($request->purchase_order_kitchen_id);

            if ($hitung) {
                $modelHeader->updateTotal([
                    'purchase_order_kitchen_subtotal' => (float) $hitung->purchase_order_kitchen_subtotal,
                    'purchase_order_kitchen_tax_amount' => (float) $hitung->purchase_order_kitchen_tax_amount,
                    'purchase_order_kitchen_grandtotal' => (float) $hitung->purchase_order_kitchen_grandtotal,
                    'purchase_order_kitchen_id' => $request->purchase_order_kitchen_id,
                ]);
            }

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, [
                'purchase_order_kitchen_id' => $request->purchase_order_kitchen_id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(GetRequest $request)
    {
        $model = new PurchaseOrderKitchenHd();

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
        $modelHeader = new PurchaseOrderKitchenHd();
        $modelDetail = new PurchaseOrderKitchenDt();

        $result = $modelHeader->getDataById($request->purchase_order_kitchen_id ?? '');

        if ($result) {
            $header = $result;
            $detailResult = $modelDetail->getDataById($result->purchase_order_kitchen_id ?? '');
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
        $modelHeader = new PurchaseOrderKitchenHd();
        $modelDetail = new PurchaseOrderKitchenDt();

        $id = $request->purchase_order_kitchen_id;

        $cek = $modelHeader->cekData($id);
        if ($cek == false) {
            return $this->responseError('Purchase Order Kitchen tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $modelDetail->deleteData($id);

            $deleteResult = $modelHeader->deleteData($id);

            if ($deleteResult == false) {
                DB::rollBack();
                return $this->responseError('Gagal menghapus data Purchase Order Kitchen', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data Purchase Order Kitchen berhasil dihapus', 200, [
                'purchase_order_kitchen_id' => $id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}

?>
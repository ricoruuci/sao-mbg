<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InvoiceDt;
use App\Models\InvoiceHd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Invoice\InsertRequest;
use App\Http\Requests\Invoice\UpdateRequest;
use App\Http\Requests\Invoice\DeleteRequest;
use App\Http\Requests\Invoice\GetRequestById;
use App\Http\Requests\Invoice\GetRequest;

class InvoiceController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $model_header = new InvoiceHd();
        $model_detail = new InvoiceDt();

        $params = [
            'invoice_date' => $request->invoice_date,
            'invoice_to' => $request->invoice_to,
            'invoice_note' => $request->invoice_note ?? '',
            'invoice_ppn' => $request->invoice_ppn ?? 0,
            'invoice_ppn_flag' => $request->invoice_ppn_flag ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
            'company_id' => $request->company_id,
        ];

        DB::beginTransaction();

        try {
            // Generate invoice code
            $invoice_code = $model_header->beforeAutoNumber($request->invoice_date, $request->company_code ?? 'MBG');
            $params['invoice_code'] = $invoice_code;

            // Insert header
            $insertheader = $model_header->insertData($params);

            if ($insertheader == false) {
                DB::rollBack();
                return $this->responseError('insert header gagal', 400);
            }

            // Process detail
            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('detail tidak boleh kosong', 400);
            }

            for ($i = 0; $i < sizeof($arrDetail); $i++) {
                $detail_total = $arrDetail[$i]['invoice_detail_qty'] * $arrDetail[$i]['invoice_detail_price'];

                $insertdetail = $model_detail->insertData([
                    'invoice_code' => $invoice_code,
                    'invoice_detail_description' => $arrDetail[$i]['invoice_detail_description'],
                    'invoice_detail_qty' => $arrDetail[$i]['invoice_detail_qty'],
                    'invoice_detail_price' => $arrDetail[$i]['invoice_detail_price'],
                    'invoice_detail_total' => $detail_total,
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();
                    return $this->responseError('insert detail gagal', 400);
                }
            }

            // Calculate and update totals
            $hitung = $model_header->hitungTotal($invoice_code);

            if ($hitung) {
                $model_header->updateTotal([
                    'invoice_subtotal' => (float) $hitung->invoice_subtotal,
                    'invoice_ppn_amount' => (float) $hitung->invoice_ppn_amount,
                    'invoice_code' => $invoice_code,
                ]);
            }

            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, ['invoice_code' => $invoice_code]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $model_header = new InvoiceHd();
        $model_detail = new InvoiceDt();

        $cek = $model_header->cekData($request->invoice_code ?? '');

        if ($cek == false) {
            return $this->responseError('invoice tidak ada atau tidak ditemukan', 400);
        }

        $params = [
            'invoice_code' => $request->invoice_code,
            'invoice_date' => $request->invoice_date,
            'invoice_to' => $request->invoice_to,
            'invoice_note' => $request->invoice_note ?? '',
            'invoice_ppn' => $request->invoice_ppn ?? 0,
            'invoice_ppn_flag' => $request->invoice_ppn_flag ?? 0,
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];

        DB::beginTransaction();

        try {
            $updateheader = $model_header->updateData($params);

            if ($updateheader == false) {
                DB::rollBack();
                return $this->responseError('update header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();
                return $this->responseError('detail tidak boleh kosong', 400);
            }

            // Delete existing detail
            $model_detail->deleteData($request->invoice_code);

            // Insert new detail
            for ($i = 0; $i < sizeof($arrDetail); $i++) {
                $detail_total = $arrDetail[$i]['invoice_detail_qty'] * $arrDetail[$i]['invoice_detail_price'];

                $insertdetail = $model_detail->insertData([
                    'invoice_code' => $request->invoice_code,
                    'invoice_detail_description' => $arrDetail[$i]['invoice_detail_description'],
                    'invoice_detail_qty' => $arrDetail[$i]['invoice_detail_qty'],
                    'invoice_detail_price' => $arrDetail[$i]['invoice_detail_price'],
                    'invoice_detail_total' => $detail_total,
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();
                    return $this->responseError('update detail gagal', 400);
                }
            }

            // Calculate and update totals
            $hitung = $model_header->hitungTotal($request->invoice_code);

            if ($hitung) {
                $model_header->updateTotal([
                    'invoice_subtotal' => (float) $hitung->invoice_subtotal,
                    'invoice_ppn_amount' => (float) $hitung->invoice_ppn_amount,
                    'invoice_code' => $request->invoice_code,
                ]);
            }

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, ['invoice_code' => $request->invoice_code]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(GetRequest $request)
    {
        $model = new InvoiceHd();
        $user = new User();

        $level = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($level->kdjabatan=='USR')
        {
            $result = $model->getAllData([
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'search_keyword' => $request->search_keyword,
                'company_id' => $level->company_id
            ]);
        }
        else
        {
            $result = $model->getAllData([
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'search_keyword' => $request->search_keyword,
            ]);
        }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getDataById(GetRequestById $request)
    {
        $model_header = new InvoiceHd();
        $model_detail = new InvoiceDt();

        $result = $model_header->getDataById($request->invoice_code ?? '');

        if ($result) {
            $header = $result;
            $detail_result = $model_detail->getDataById($result->invoice_code ?? '');
            $detail = !empty($detail_result) ? $detail_result : [];
        }
        else {
            $header = [];
            $detail = [];
        }

        $response = [
            'header' => $header,
            'detail' => $detail
        ];

        return $this->responseData($response);
    }

    public function deleteData(DeleteRequest $request)
    {
        $model_header = new InvoiceHd();
        $model_detail = new InvoiceDt();

        $id = $request->invoice_code;

        $cek = $model_header->cekData($request->invoice_code);
        if ($cek == false) {
            return $this->responseError('Invoice tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            // Delete detail first
            $model_detail->deleteData($id);

            // Delete header
            $deleteResult = $model_header->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data Invoice', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data Invoice berhasil dihapus', 200, ['invoice_code' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}

?>

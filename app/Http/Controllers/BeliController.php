<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BeliDt;
use App\Models\BeliHd;
use App\Models\Satuan;
use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Beli\InsertRequest;
use App\Http\Requests\Beli\UpdateRequest;
use App\Http\Requests\Beli\DeleteRequest;
use App\Http\Requests\Beli\GetRequestById;
use App\Http\Requests\Beli\GetRequest;

class BeliController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $model_header = new BeliHd();
        $model_detail = new BeliDt();
        $model_supplier = new Supplier();
        $model_satuan = new Satuan();
        $model_bb = new BahanBaku();

        $cek = $model_supplier->cekData($request->supplier_id ?? '');

        if ($cek == false) {

            return $this->responseError('supplier tidak ada atau tidak ditemukan', 400);
        }

        $params = [
            'transdate' => $request->transdate,
            'supplier_id' => $request->supplier_id,
            'ppn' => $request->ppn ?? 0,
            'note' => $request->note ?? '',
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
            'company_id' => Auth::user()->currentAccessToken()['company_id'],
        ];

        DB::beginTransaction();

        try {
            $hasilpoid = $model_header->beforeAutoNumber($request->transdate,Auth::user()->currentAccessToken()['company_code']);

            $params['nota_beli'] = $hasilpoid;

            $insertheader = $model_header->insertData($params);

            if ($insertheader == false) {
                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('detail tidak boleh kosong', 400);
            }

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $model_bb->cekData($arrDetail[$i]['bahan_baku_id'] ?? '');

                if ($cek == false) {
                    DB::rollBack();

                    return $this->responseError('bahan baku tidak ada atau tidak ditemukan', 400);
                }

                $cek = $model_satuan->cekData($arrDetail[$i]['satuan'] ?? '');

                if ($cek == false) {
                    DB::rollBack();

                    return $this->responseError('satuan tidak ada atau tidak ditemukan', 400);
                }

                $insertdetail = $model_detail->insertData([
                    'nota_beli' => $hasilpoid,
                    'supplier_id' => $request->supplier_id,
                    'bahan_baku_id' => $arrDetail[$i]['bahan_baku_id'],
                    'qty' => $arrDetail[$i]['qty'],
                    'price' => $arrDetail[$i]['price'],
                    'satuan' => $arrDetail[$i]['satuan'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }
            }

            $hitung = $model_header->hitungTotal($hasilpoid);

            $model_header->updateTotal([
                'grand_total' => $hitung->grand_total,
                'sub_total' => $hitung->sub_total,
                'total_ppn' => $hitung->total_ppn,
                'nota_beli' => $hasilpoid,
            ]);

            $model_detail->deleteAllItem($hasilpoid);

            $model_detail->insertAllItem($hasilpoid,Auth::user()->currentAccessToken()['company_id']);

            $model_detail->updateAllTransaction([
                'id' => $hasilpoid,
                'transdate' => $request->transdate,
                'fgtrans' => 1,
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);

            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, ['nota_beli' => $hasilpoid]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }

    }

    public function UpdateData(UpdateRequest $request)
    {
        $model_header = new BeliHd();
        $model_detail = new BeliDt();
        $model_supplier = new Supplier();
        $model_satuan = new Satuan();
        $model_bb = new BahanBaku();

        $cek = $model_header->cekData($request->nota_beli ?? '');

        if ($cek == false) {

            return $this->responseError('nota beli tidak ada atau tidak ditemukan', 400);
        }

        $cek = $model_supplier->cekData($request->supplier_id ?? '');

        if ($cek == false) {

            return $this->responseError('supplier tidak ada atau tidak ditemukan', 400);
        }

        $params = [
            'nota_beli' => $request->nota_beli,
            'transdate' => $request->transdate,
            'supplier_id' => $request->supplier_id,
            'ppn' => $request->ppn ?? 0,
            'note' => $request->note ?? '',
            'upduser' => Auth::user()->currentAccessToken()['namauser'],
        ];    

        DB::beginTransaction();

        try {

            $insertheader = $model_header->updateData($params);

            if ($insertheader == false) {
                DB::rollBack();

                return $this->responseError('update header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            if (empty($arrDetail) || !is_array($arrDetail)) {
                DB::rollBack();

                return $this->responseError('detail tidak boleh kosong', 400);
            }

            $model_detail->deleteData($request->nota_beli);

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $model_bb->cekData($arrDetail[$i]['bahan_baku_id'] ?? '');

                if ($cek == false) {
                    DB::rollBack();

                    return $this->responseError('bahan baku tidak ada atau tidak ditemukan', 400);
                }

                $cek = $model_satuan->cekData($arrDetail[$i]['satuan'] ?? '');

                if ($cek == false) {
                    DB::rollBack();

                    return $this->responseError('satuan tidak ada atau tidak ditemukan', 400);
                }

                $insertdetail = $model_detail->insertData([
                    'nota_beli' => $request->nota_beli,
                    'supplier_id' => $request->supplier_id,
                    'bahan_baku_id' => $arrDetail[$i]['bahan_baku_id'],
                    'qty' => $arrDetail[$i]['qty'],
                    'price' => $arrDetail[$i]['price'],
                    'satuan' => $arrDetail[$i]['satuan'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser']
                ]);

                if ($insertdetail == false) {
                    DB::rollBack();

                    return $this->responseError('update detail gagal', 400);
                }
            }

            $hitung = $model_header->hitungTotal($request->nota_beli);

            $model_header->updateTotal([
                'grand_total' => $hitung->grand_total,
                'sub_total' => $hitung->sub_total,
                'total_ppn' => $hitung->total_ppn,
                'nota_beli' => $request->nota_beli,
            ]);

            $model_detail->deleteAllItem($request->nota_beli);

            $model_detail->insertAllItem($request->nota_beli,Auth::user()->currentAccessToken()['company_id']);

            $model_detail->updateAllTransaction([
                'id' => $request->nota_beli,
                'transdate' => $request->transdate,
                'fgtrans' => 1,
                'company_id' => Auth::user()->currentAccessToken()['company_id']
            ]);

            DB::commit();

            return $this->responseSuccess('update berhasil', 200, ['nota_beli' => $request->nota_beli]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }

    }

    public function getListData(GetRequest $request)
    {
        $model = new BeliHd();
        $user = new User();

        $level = $user->cekLevel(Auth::user()->currentAccessToken()['namauser']);

        if ($level->kdjabatan=='ADM')
        {
            $result = $model->getAllData([
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'search_keyword' => $request->search_keyword,
                'supplier_keyword' => $request->supplier_keyword,
            ]);
        }
        else
        {
            $result = $model->getAllData([
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'search_keyword' => $request->search_keyword,
                'supplier_keyword' => $request->supplier_keyword,
                'company_id' => $level->company_id
            ]);
        }

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getDataById(GetRequestById $request)
    {
        $model_header = new BeliHd();

        $model_detail = new BeliDt();

        $result = $model_header->getDataById($request->nota_beli ?? '');

        if ($result) {
            $header = $result;

            $detail_result = $model_detail->getDataById($result->nota_beli ?? '');

            $detail = !empty($detail_result) ? $detail_result : [];
        }
        else {
            $header = [];
            $detail = [];
        }

        $response = [
            'header' => $header,
            'detail' => $detail,
        ];

        return $this->responseData($response);

    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new BeliHd();
        $model_detail = new BeliDt();

        $id = $request->nota_beli;

        $cek = $model->cekData($request->nota_beli);
        if ($cek == false) {
            return $this->responseError('Nota beli tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try {
            $model_detail->deleteAllItem($id);

            $deleteResult = $model->deleteData($id);

            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data Nota Beli', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data Nota Beli berhasil dihapus', 200, ['nota_beli' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>

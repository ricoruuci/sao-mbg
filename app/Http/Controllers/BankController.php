<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Bank\GetRequest;
use App\Http\Requests\Bank\GetRequestById;  
use App\Http\Requests\Bank\InsertRequest;
use App\Http\Requests\Bank\UpdateRequest;
use App\Http\Requests\Bank\DeleteRequest;

class BankController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getListData(GetRequest $request)
    {
        $model = new Bank();

        $result = $model->getAllData([
            'fgactive' => $request->fgactive ?? 'all',
            'search_keyword' => $request->input('search_keyword') ?? ''
        ]);
        
        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getDataById(GetRequestById $request)
    {
        $model = new Bank();

        $id = $request->bank_id;

        $cek = $model->cekData($id);
        if (is_null($cek)) {
            return $this->responseError('Data bank tidak ditemukan', 404);
        }

        $result = $model->getDataById($id);

        return $this->responseData($result);
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new Bank();
        $modelGroup = new Rekening();

        $id = $request->bank_id;

        $cek = $model->cekData($id);
        if (is_null($cek)) {
            return $this->responseError('Data bank tidak ditemukan', 404);
        }

        $cekGroup = $modelGroup->cekData($request->rekening_id);
        if (is_null($cekGroup)) {
            return $this->responseError('Data Group Rekening tidak ditemukan', 404);
        }   

        DB::beginTransaction();

        try 
        {
            $data = [
                'bankid' => $id,
                'bankname' => $request->input('bank_name'),
                'rekeningid' => $request->input('rekening_id'),
                'note' => $request->input('note', ''),
                'fgactive' => $request->input('fgactive'),
                'upduser' => Auth::user()->currentAccessToken()['namauser']
            ];

            $result = $model->updateData($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data bank', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data bank berhasil disimpan', 200, ['bank' => $request->bank_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function insertData(InsertRequest $request)
    {
        $model = new Bank();
        $modelGroup = new Rekening();

        $cek = $model->cekData($request->bank_id);
        if ($cek) {
            return $this->responseError('Data bank sudah ada', 404);
        }

        $cekGroup = $modelGroup->cekData($request->rekening_id);
        if (is_null($cekGroup)) {
            return $this->responseError('Data Group Rekening tidak ditemukan', 404);
        }  

        DB::beginTransaction();

        try 
        {
            $data = [
                'bankid' => $request->bank_id,
                'bankname' => $request->input('bank_name'),
                'rekeningid' => $request->input('rekening_id'),
                'note' => $request->input('note', ''),
                'fgactive' => $request->input('fgactive'),
                'upduser' => Auth::user()->currentAccessToken()['namauser']
            ];

            $result = $model->insertData($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data bank', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data bank berhasil disimpan', 200, ['bank' => $request->bank_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new Bank();

        $id = $request->bank_id;

        $cek = $model->cekData($id);
        if (is_null($cek)) {
            return $this->responseError('Data bank tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try 
        {
            $result = $model->deleteData($id);

            if ($result == false) {
                return $this->responseError('Gagal menghapus data bank', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data bank berhasil dihapus', 200, ['bank' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>

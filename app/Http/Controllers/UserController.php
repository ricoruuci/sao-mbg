<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cabang; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\GetRequest;
use App\Http\Requests\User\GetRequestById;  
use App\Http\Requests\User\InsertRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\DeleteRequest;
use App\Http\Requests\User\UpdatePassword;

class UserController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function getListData(GetRequest $request)
    {
        $model = new User();

        $result = $model->getListData();
        
        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

    public function getDataById(GetRequestById $request)
    {
        $model = new User();

        $id = $request->userid;

        $cek = $model->cekUserId($id);
        if (is_null($cek)) {
            return $this->responseError('Data User tidak ditemukan', 404);
        }

        $result = $model->getDataById($id);

        return $this->responseData($result);
    }

    public function updateData(UpdateRequest $request)
    {
        $model = new User();
        $modelGroup = new Cabang();

        $id = $request->userid;

        $cek = $model->cekUserId($id);
        if (is_null($cek)) {
            return $this->responseError('Data User tidak ditemukan', 404);
        }

        $cekGroup = $modelGroup->cekData($request->company_id);
        if (is_null($cekGroup)) {
            return $this->responseError('Data Cabang tidak ditemukan', 404);
        }   

        DB::beginTransaction();

        try 
        {
            $data = [
                'userid' => $id,
                'company_id' => $request->input('company_id'),
                'group_user' => $request->input('group_user')
            ];

            $result = $model->updateAllData($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data User', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data User berhasil disimpan', 200, ['userid' => $request->userid]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function insertData(InsertRequest $request)
    {
        $model = new User();
        $modelGroup = new Cabang();

        $cek = $model->cekUserId($request->userid);
        if ($cek) {
            return $this->responseError('Data User sudah ada', 404);
        }

        $cekGroup = $modelGroup->cekData($request->company_id);
        if (is_null($cekGroup)) {
            return $this->responseError('Data Cabang tidak ditemukan', 404);
        }  

        DB::beginTransaction();

        try 
        {
            $data = [
                'userid' => $request->userid,
                'password' => $request->input('password'),
                'company_id' => $request->input('company_id'),
                'group_user' => $request->input('group_user', '')
            ];

            $result = $model->insertData($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data User', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data User berhasil disimpan', 200, ['userid' => $request->userid]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new User();

        $id = $request->userid;

        $cek = $model->cekUserId($id);
        if (is_null($cek)) {
            return $this->responseError('Data User tidak ditemukan', 404);
        }

        DB::beginTransaction();

        try 
        {
            $result = $model->deleteUser($id);

            if ($result == false) {
                return $this->responseError('Gagal menghapus data User', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data User berhasil dihapus', 200, ['userid' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updatePassword(UpdatePassword $request)
    {
        $model = new User();

        $id = $request->userid;

        $cek = $model->cekUserId($id);
        if (is_null($cek)) {
            return $this->responseError('Data User tidak ditemukan', 404);
        }

        $params = [
            'userid' => $id,
            'password' => $request->old_password
        ];

        $cek = $model->cekPassword($params);
        if (is_null($cek) | $cek == false) {
            return $this->responseError('Data User tidak ditemukan atau password lama salah', 404);
        }

        DB::beginTransaction();

        try 
        {
            $data = [
                'userid' => $id,
                'password' => $request->input('new_password')
            ];

            $result = $model->updatePassword($data);

            if ($result == false) {
                return $this->responseError('Gagal menyimpan data User', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data User berhasil disimpan', 200, ['userid' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

}

?>

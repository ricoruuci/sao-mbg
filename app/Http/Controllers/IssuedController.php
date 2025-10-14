<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Issued;
use App\Models\BahanBaku;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Issued\InsertRequest;
use App\Http\Requests\Issued\GetRequest;

class IssuedController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(InsertRequest $request)
    {
        $model_detail = new Issued();

        $model_bahan_baku = new BahanBaku();

        $params = [
            'tanggal' => $request->tanggal,
            'company_id' => Auth::user()->currentAccessToken()['company_id'],
        ];

        DB::beginTransaction();

        try {

            $arrDetail = $request->input('detail');

            $deletedetail = $model_detail->deleteData($params);

            if (!empty($arrDetail) || is_array($arrDetail)) {
                
                for ($i = 0; $i < sizeof($arrDetail); $i++) {

                    $cek = $model_bahan_baku->cekData($arrDetail[$i]['bahan_baku_id'] ?? '');

                    if ($cek == false) {
                        DB::rollBack();

                        return $this->responseError('bahan baku tidak ada atau tidak ditemukan', 400);
                    }

                    $cek = $model_bahan_baku->cekDataSatuan($arrDetail[$i]['bahan_baku_id'] ?? '');

                    if ($cek == false) {
                        DB::rollBack();

                        return $this->responseError('satuan tidak terdaftar untuk bahan baku ini', 400);
                    }

                    $insertdetail = $model_detail->insertData([
                        'tanggal' => $params['tanggal'],
                        'bahan_baku_id' => $arrDetail[$i]['bahan_baku_id'],
                        'satuan' => $arrDetail[$i]['satuan'],
                        'jumlah' => $arrDetail[$i]['jumlah'],
                        'upduser' => Auth::user()->currentAccessToken()['namauser'],
                        'company_id' => $params['company_id']
                    ]);

                    if ($insertdetail == false) {
                        DB::rollBack();

                        return $this->responseError('insert detail gagal', 400);
                    }
                }
            }

            $model_detail->deleteAllItem($params);

            $model_detail->insertAllItem($params);

            DB::commit();

            return $this->responseSuccess('insert berhasil', 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }

    }

    public function getListData(GetRequest $request)
    {
        $model_detail = new Issued();

        $params = [
            'tanggal' => $request->tanggal,
            'company_id' => Auth::user()->currentAccessToken()['company_id'],
        ];

        $result = $model_detail->getAllData($params);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);

    }

}

?>

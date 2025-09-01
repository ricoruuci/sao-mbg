<?php

namespace App\Http\Controllers\IN\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IN\Master\INMsProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $group = new INMsProduct();

        $validator = Validator::make($request->all(), $group::$rulesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            DB::beginTransaction();

            try {
                $insert = $group->insertData([
                    'productid' => $request->input('productid'),
                    'productdesc' => $request->input('productdesc'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($insert) {
                    $result = [
                        'success' => true
                    ];
                } else {
                    DB::rollBack();

                    return $this->responseError('insert gagal', 400);
                }

                DB::commit();

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function updateAllData(Request $request)
    {

        $group = new INMsProduct();


        $validator = Validator::make($request->all(), $group::$rulesUpdateAll);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {
            $cek = $group->cekProduct($request->input('productid'));

            if ($cek == false) {

                return $this->responseError('kode product tidak terdaftar dalam master', 400);
            }

            DB::beginTransaction();

            try {
                $updated = $group->updateAllData([
                    'productid' => $request->input('productid'),
                    'productdesc' => $request->input('productdesc'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                ]);

                if ($updated) {
                    $result = [
                        'success' => true,
                        'updated' => $updated
                    ];
                } else {
                    DB::rollBack();

                    return $this->responseError('update gagal', 400);
                }

                DB::commit();

                return $result;
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->responseError($e->getMessage(), 400);
            }
        }
    }

    public function getListData(Request $request)
    {
        $product = new INMsProduct();

        $result = $product->getListData([
            'productidkeyword' => $request->input('productidkeyword'),
            'productdesckeyword' => $request->input('productdesckeyword')
        ]);

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function deleteData(Request $request)
    {
        $group = new INMsProduct();

        $cek = $group->cekProduct($request->input('productid'));

        if ($cek == false) {

            return $this->responseError('kode product tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $group->deleteData([
                'productid' => $request->input('productid'),
            ]);

            if ($deleted) {
                $result = [
                    'success' => true,
                    'deleted' => $deleted
                ];
            } else {
                DB::rollBack();

                return $this->responseError('delete gagal', 400);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}

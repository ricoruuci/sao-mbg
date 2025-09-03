<?php

namespace App\Http\Controllers\IN\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IN\Master\INMsItem;
use App\Models\IN\Master\INMsGroup;
use App\Models\IN\Master\INMsProduct;
use App\Models\IN\Master\INMsUOM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $barang = new INMsItem();

        $group = new INMsGroup();

        $product = new INMsProduct();

        $validator = Validator::make($request->all(), $barang::$rulesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $cek = $group->cekGroup($request->input('groupid'));

            if ($cek == false) {

                return $this->responseError('kode group tidak terdaftar dalam master', 400);
            }

            $cek = $product->cekProduct($request->input('productid'));

            if ($cek == false) {

                return $this->responseError('kode product tidak terdaftar dalam master', 400);
            }

            $kodebarang = $barang->beforeAutoNumber($request->input('groupid'), $request->input('productid'));

            DB::beginTransaction();

            try {
                $insert = $barang->insertData([
                    'itemid' => $kodebarang,
                    'itemname' => $request->input('itemname'),
                    'productid' => $request->input('productid'),
                    'groupid' => $request->input('groupid'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'note' => $request->input('note') ?? ''
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

    public function getListData(Request $request)
    {
        $supplier = new INMsItem();

        if ($request->input('itemid')) {

            $result = $supplier->getdata(
                [
                    'itemid' => $request->input('itemid')
                ]
            );

            return $this->responseData($result);
        } else {
            $result = $supplier->getListData([
                'itemidkeyword' => $request->input('itemidkeyword') ?? '',
                'itemnamekeyword' => $request->input('itemnamekeyword') ?? '',
                'groupidkeyword' => $request->input('groupidkeyword') ?? '',
                'groupdesckeyword' => $request->input('groupdesckeyword') ?? '',
                'productidkeyword' => $request->input('productidkeyword') ?? '',
                'productdesckeyword' => $request->input('productdesckeyword') ?? '',
                'sortby' => $request->input('sortby') ?? 'old',
            ]);

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }

    public function updateAllData(Request $request)
    {
        $barang = new INMsItem();

        $group = new INMsGroup();

        $product = new INMsProduct();

        $validator = Validator::make($request->all(), $barang::$rulesUpdateAll);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {
            $cek = $barang->cekBarang($request->input('itemid'));

            if ($cek == false) {

                return $this->responseError('kode barang tidak terdaftar dalam master', 400);
            }

            $cek = $group->cekGroup($request->input('groupid'));

            if ($cek == false) {

                return $this->responseError('kode group tidak terdaftar dalam master', 400);
            }

            $cek = $product->cekProduct($request->input('productid'));

            if ($cek == false) {

                return $this->responseError('kode product tidak terdaftar dalam master', 400);
            }

            DB::beginTransaction();

            try {
                $updated = $barang->updateAllData([
                    'itemid' => $request->input('itemid'),
                    'itemname' => $request->input('itemname'),
                    'productid' => $request->input('productid'),
                    'groupid' => $request->input('groupid'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'note' => $request->input('note') ?? ''
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

    public function deleteData(Request $request)
    {
        $barang = new INMsItem();

        $cek = $barang->cekBarang($request->input('itemid'));

        if ($cek == false) {

            return $this->responseError('kode barang tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $barang->deleteData([
                'itemid' => $request->input('itemid')
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

<?php

namespace App\Http\Controllers\AR\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AR\Master\ARMsSales;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $sales = new ARMsSales();

        $validator = Validator::make($request->all(), $sales::$rulesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $kodesales = $sales->beforeAutoNumber($request->input('salesname'));

            DB::beginTransaction();

            try {
                $insert = $sales->insertData([
                    'salesid' => $kodesales,
                    'salesname' => $request->input('salesname'),
                    'address' => $request->input('address'),
                    'phone' => $request->input('phone'),
                    'hp' => $request->input('hp'),
                    'email' => $request->input('email'),
                    'note' => $request->input('note'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'jabatan' => $request->input('jabatan'),
                    'uangmakan' => $request->input('uangmakan'),
                    'uangbulanan' => $request->input('uangbulanan'),
                    'fgactive' => $request->input('fgactive'),
                    'tglgabung' => $request->input('tglgabung'),
                    'limitkasbon' => $request->input('limitkasbon'),
                    'kerajinan' => $request->input('kerajinan'),
                    'tomzet' => $request->input('tomzet'),
                    'kom1' => $request->input('kom1'),
                    'kom2' => $request->input('kom2'),
                    'kom3' => $request->input('kom3'),
                    'kom4' => $request->input('kom4')
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
        $sales = new ARMsSales();

        if ($request->input('salesid')) {

            $cek = $sales->cekSales($request->input('salesid'));

            if ($cek == false) {

                return $this->responseError('kode sales tidak terdaftar dalam master', 400);
            }

            $result = $sales->getdata(
                [
                    'salesid' => $request->input('salesid')
                ]
            );

            return $this->responseData($result);
        } else {
            $result = $sales->getListData([
                'salesidkeyword' => $request->input('salesidkeyword') ?? '',
                'salesnamekeyword' => $request->input('salesnamekeyword') ?? '',
                'sortby' => $request->input('sortby') ?? 'salesid'
            ]);

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }



    public function updateAllData(Request $request, $salesid)
    {
        $sales = new ARMsSales();

        $validator = Validator::make($request->all(), $sales::$rulesUpdateAll);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        } else {

            $cek = $sales->cekSales($salesid);

            if ($cek == false) {

                return $this->responseError('kode sales tidak terdaftar dalam master', 400);
            }

            DB::beginTransaction();

            try {
                $updated = $sales->updateAllData([
                    'salesid' => $salesid,
                    'salesname' => $request->input('salesname'),
                    'address' => $request->input('address'),
                    'phone' => $request->input('phone'),
                    'hp' => $request->input('hp'),
                    'email' => $request->input('email'),
                    'note' => $request->input('note'),
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'jabatan' => $request->input('jabatan'),
                    'uangmakan' => $request->input('uangmakan'),
                    'uangbulanan' => $request->input('uangbulanan'),
                    'fgactive' => $request->input('fgactive'),
                    'tglgabung' => $request->input('tglgabung'),
                    'limitkasbon' => $request->input('limitkasbon'),
                    'kerajinan' => $request->input('kerajinan'),
                    'tomzet' => $request->input('tomzet'),
                    'kom1' => $request->input('kom1'),
                    'kom2' => $request->input('kom2'),
                    'kom3' => $request->input('kom3'),
                    'kom4' => $request->input('kom4')
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

    public function deleteData(Request $request, $salesid)
    {
        $sales = new ARMsSales();

        $cek = $sales->cekSales($salesid);

        if ($cek == false) {

            return $this->responseError('kode sales tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $sales->deleteData([
                'salesid' => $salesid
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

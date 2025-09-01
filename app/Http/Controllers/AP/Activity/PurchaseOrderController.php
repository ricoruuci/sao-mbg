<?php

namespace App\Http\Controllers\AP\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AP\Activity\PurchaseOrderHd;
use App\Models\AP\Activity\PurchaseOrderDt;
use App\Models\AP\Master\APMsSupplier;
use App\Models\AR\Master\ARMsSales;
use App\Models\IN\Master\INMsItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $sales = new PurchaseOrderHd();

        $modelDetail = new PurchaseOrderDt();

        $mssales = new ARMsSales();

        $mscustomer = new APMsSupplier();

        $msitem = new INMsItem();

        $validator = Validator::make($request->all(), $sales::$rulesInsert, $sales::$messagesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        }

        $validatorDetail = Validator::make($request->all(), $modelDetail::$rulesInsert, $modelDetail::$messagesInsert);

        if ($validatorDetail->fails()) {
            return $this->responseError($validatorDetail->messages(), 400);
        }

        $cek = $mscustomer->cekSupplier($request->input('suppid'));

        if ($cek == false) {

            return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
        }

        $cek = $mssales->cekSales($request->input('purchasingid'));

        if ($cek == false) {

            return $this->responseError('kode purchasing tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $hasilpoid = $sales->beforeAutoNumber($request->input('transdate'));

            $insertheader = $sales->insertData([
                'poid' => $hasilpoid,
                'transdate' => $request->input('transdate'),
                'currid' => $request->input('currid'),
                'suppname' => $request->input('suppname'),
                'up' => $request->input('up'),
                'purchasingid' => $request->input('purchasingid'),
                'telp' => $request->input('telp'),
                'fax' => $request->input('fax'),
                'email' => $request->input('email'),
                'note' => $request->input('note'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'fgtax' => $request->input('fgtax'),
                'discamount' => $request->input('discamount'),
                'suppid' => $request->input('suppid'),
                'soid' => $request->input('soid'),
                'nilaitax' => $request->input('nilaitax')
            ]);

            if ($insertheader) {
                $result = [
                    'success' => true
                ];
            } else {
                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode barang tidak terdaftar dalam master', 400);
                }

                $insertdetail = $modelDetail->insertData([
                    'poid' => $hasilpoid,
                    'urut' => $arrDetail[$i]['urut'],
                    'itemid' => $arrDetail[$i]['itemid'],
                    'itemname' => $arrDetail[$i]['itemname'],
                    'note' => $arrDetail[$i]['note'],
                    'qty' => $arrDetail[$i]['qty'],
                    'price' => $arrDetail[$i]['price'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'partno' => $arrDetail[$i]['partno']
                ]);

                if ($insertdetail) {
                    $result = [
                        'success' => true
                    ];
                } else {
                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }
            }

            $hitung = $sales->hitungTotal([
                'poid' => $hasilpoid
            ]);

            $sales->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'poid' => $hasilpoid
            ]);

            $result = [
                'success' => true
            ];

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function updateAllData(Request $request)
    {
        $sales = new PurchaseOrderHd();

        $modelDetail = new PurchaseOrderDt();

        $mssales = new ARMsSales();

        $mscustomer = new APMsSupplier();

        $msitem = new INMsItem();

        $validator = Validator::make($request->all(), $sales::$rulesUpdateAll, $sales::$messagesUpdate);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        }

        $validatorDetail = Validator::make($request->all(), $modelDetail::$rulesInsert, $modelDetail::$messagesInsert);

        if ($validatorDetail->fails()) {
            return $this->responseError($validatorDetail->messages(), 400);
        }

        $cek = $sales->cekSalesorder($request->input('poid'));

        if ($cek == false) {

            return $this->responseError('nomor purchase order tidak terdaftar', 400);
        }

        $cek = $mscustomer->cekSupplier($request->input('suppid'));

        if ($cek == false) {

            return $this->responseError('kode supplier tidak terdaftar dalam master', 400);
        }

        $cek = $mssales->cekSales($request->input('purchasingid'));

        if ($cek == false) {

            return $this->responseError('kode purchasing tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $insertheader = $sales->updateAllData([
                'poid' => $request->input('poid'),
                'transdate' => $request->input('transdate'),
                'currid' => $request->input('currid'),
                'suppname' => $request->input('suppname'),
                'up' => $request->input('up'),
                'purchasingid' => $request->input('purchasingid'),
                'telp' => $request->input('telp'),
                'fax' => $request->input('fax'),
                'email' => $request->input('email'),
                'note' => $request->input('note'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'fgtax' => $request->input('fgtax'),
                'discamount' => $request->input('discamount'),
                'suppid' => $request->input('suppid'),
                'soid' => $request->input('soid'),
                'nilaitax' => $request->input('nilaitax')
            ]);

            if ($insertheader) {
                $result = [
                    'success' => true
                ];
            } else {
                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $deletedetail = $modelDetail->deleteData([
                'poid' => $request->input('poid')
            ]);

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {
                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode barang tidak terdaftar dalam master', 400);
                }

                $insertdetail = $modelDetail->insertData([
                    'poid' => $request->input('poid'),
                    'urut' => $arrDetail[$i]['urut'],
                    'itemid' => $arrDetail[$i]['itemid'],
                    'itemname' => $arrDetail[$i]['itemname'],
                    'note' => $arrDetail[$i]['note'],
                    'qty' => $arrDetail[$i]['qty'],
                    'price' => $arrDetail[$i]['price'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'partno' => $arrDetail[$i]['partno']
                ]);

                if ($insertdetail) {
                    $result = [
                        'success' => true
                    ];
                } else {
                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }
            }

            $hitung = $sales->hitungTotal([
                'poid' => $request->input('poid')
            ]);

            $sales->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'poid' => $request->input('poid')
            ]);

            $result = [
                'success' => true
            ];

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(Request $request)
    {
        $sales = new PurchaseOrderHd();
        $salesdt = new PurchaseOrderDt();

        if ($request->input('poid')) {

            $resultheader = $sales->getdata(
                [
                    'poid' => $request->input('poid')
                ]
            );

            $resultdetail = $salesdt->getdata(
                [
                    'poid' => $request->input('poid')
                ]
            );

            $result = [
                'header' => $resultheader,
                'detail' => $resultdetail
            ];

            return $this->responseData($result);
        } else {

            $result = $sales->getListData(
                [
                    'dari' => $request->input('dari'),
                    'sampai' => $request->input('sampai'),
                    'suppidkeyword' => $request->input('suppidkeyword') ?? '',
                    'suppnamekeyword' => $request->input('suppnamekeyword') ?? '',
                    'poidkeyword' => $request->input('poidkeyword') ?? '',
                    'purchasingidkeyword' => $request->input('purchasingidkeyword') ?? '',
                    'purchasingnamekeyword' => $request->input('purchasingnamekeyword') ?? '',
                    'soidkeyword' => $request->input('soidkeyword') ?? '',
                    'sortby' => $request->input('sortby') ?? 'old'
                ]
            );

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }

    public function deleteData(Request $request)
    {
        $sales = new PurchaseOrderHd();

        $cek = $sales->cekSalesorder($request->input('poid'));

        if ($cek == false) {

            return $this->responseError('nomor purchase order tidak terdaftar', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $sales->deleteData([
                'poid' => $request->input('poid')
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

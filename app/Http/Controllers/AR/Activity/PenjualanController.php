<?php

namespace App\Http\Controllers\AR\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AR\Activity\PenjualanHd;
use App\Models\AR\Activity\PenjualanDt;
use App\Models\AR\Activity\PenjualanSN;
// use App\Models\AR\Activity\SalesOrderBiaya;
use App\Models\AR\Master\ARMsCustomer;
use App\Models\AR\Master\ARMsSales;
use App\Models\IN\Master\INMsItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $sales = new PenjualanHd();
        $modelDetail = new PenjualanDt();
        $modelBiaya = new PenjualanSN();


        $mssales = new ARMsSales();
        $mscustomer = new ARMsCustomer();
        $msitem = new INMsItem();

        $validator = Validator::make($request->all(), $sales::$rulesInsert, $sales::$messagesInsert);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        }

        $validatorDetail = Validator::make($request->all(), $modelDetail::$rulesInsert, $modelDetail::$messagesInsert);

        if ($validatorDetail->fails()) {
            return $this->responseError($validatorDetail->messages(), 400);
        }

        $validatorBiaya = Validator::make($request->all(), $modelBiaya::$rulesInsert, $modelBiaya::$messagesInsert);

        if ($validatorBiaya->fails()) {
            return $this->responseError($validatorBiaya->messages(), 400);
        }

        $cek = $mscustomer->cekCustomer($request->input('custid'));

        if ($cek == false) {

            return $this->responseError('kode pelanggan tidak terdaftar dalam master', 400);
        }

        $custname = $cek->CustName;

        $cek = $mssales->cekSales($request->input('salesid'));

        if ($cek == false) {

            return $this->responseError('kode sales tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $hasilsaleid = $sales->beforeAutoNumber($request->input('transdate'));
            //$soid = $sales->CariSO($request->input('soid'));
            //dd(var_dump($soid));

            $insertheader = $sales->insertData([
                'saleid' => $hasilsaleid,
                'poid' => $request->input('poid'),
                'transdate' => $request->input('transdate'),
                'custid' => $request->input('custid'),
                'salesid' => $request->input('salesid'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'fgtax' => $request->input('fgtax') ?? 'T',
                'nilaitax' => $request->input('nilaitax') ?? '',
                'fpsid' => $request->input('fpsid') ?? '',
                'note' => $request->input('note') ?? 0
            ]);

            //dd(var_dump($insertheader));

            if ($insertheader == false) {

                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $deleteallitem = $sales->deleteAllItem([
                'saleid' => $hasilsaleid,
            ]);

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {

                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode mobil tidak terdaftar dalam master', 400);
                }

                $insertdetail = $modelDetail->insertData([
                    'saleid' => $hasilsaleid,
                    'itemid' => $arrDetail[$i]['itemid'],
                    'price' => $arrDetail[$i]['price'],
                    'qty' => $arrDetail[$i]['qty'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'note' => $arrDetail[$i]['note']
                ]);

                if ($insertdetail == false) {

                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }

                $insertallitem = $sales->insertAllItem([
                    'saleid' => $hasilsaleid,
                    'transdate' => $request->input('transdate'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'price' => $arrDetail[$i]['price'],
                    'qty' => $arrDetail[$i]['qty'],
                    'custname' => $custname

                ]);

                //dd(var_dump($insertheader));

                if ($insertallitem == false) {

                    DB::rollBack();

                    return $this->responseError('insert allitem gagal', 400);
                }

                if (isset($arrDetail[$i]['detailsn'])) {

                    $arrDetailsn = $arrDetail[$i]['detailsn'];

                    if (sizeof($arrDetailsn) <> $arrDetail[$i]['qty']) {

                        DB::rollBack();

                        return $this->responseError('jumlah No Polisi mobil (kode :' . $arrDetail[$i]['itemid'] . ') tidak sama dengan jumlah detail', 400);
                    }

                    for ($u = 0; $u < sizeof($arrDetailsn); $u++) {

                        /*$cek = $msitem->cekBarang($arrDetailsn[$u]['packageid']);

                        if ($cek == false) {

                            DB::rollBack();

                            return $this->responseError('kode package tidak terdaftar dalam master', 400);
                        }*/

                        $insertdetail = $modelBiaya->insertData([
                            'snid' => $arrDetailsn[$u]['snid'],
                            'saleid' => $hasilsaleid,
                            'itemid' => $arrDetail[$i]['itemid'],
                            'price' => $arrDetail[$i]['price'],
                            'modal' => $arrDetailsn[$u]['modal'],
                            'purchaseid' => $arrDetailsn[$u]['purchaseid'],
                            'fgsn' => $arrDetailsn[$u]['fgsn'],
                            'upduser' => Auth::user()->currentAccessToken()['namauser']
                        ]);

                        if ($insertdetail == false) {
                            DB::rollBack();

                            return $this->responseError('insert Nopol gagal', 400);
                        }
                    }
                }
            }

            $hitung = $sales->hitungTotal([
                'saleid' => $hasilsaleid
            ]);

            $sales->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'ppn' => $hitung->ppn,
                'subtotal' => $hitung->subtotal,
                'saleid' => $hasilsaleid
            ]);


            DB::commit();

            return $this->responseSuccess('insert berhasil', 200, ['saleid' => $hasilsaleid]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function updateAllData(Request $request)
    {
        $sales = new PenjualanHd();
        $modelDetail = new PenjualanDt();
        $modelBiaya = new PenjualanSN();

        $mssales = new ARMsSales();
        $mscustomer = new ARMsCustomer();
        $msitem = new INMsItem();

        $validator = Validator::make($request->all(), $sales::$rulesUpdateAll, $sales::$messagesUpdate);

        if ($validator->fails()) {
            return $this->responseError($validator->messages(), 400);
        }

        $validatorDetail = Validator::make($request->all(), $modelDetail::$rulesInsert, $modelDetail::$messagesInsert);

        if ($validatorDetail->fails()) {
            return $this->responseError($validatorDetail->messages(), 400);
        }

        $validatorBiaya = Validator::make($request->all(), $modelBiaya::$rulesInsert, $modelBiaya::$messagesInsert);

        if ($validatorBiaya->fails()) {
            return $this->responseError($validatorBiaya->messages(), 400);
        }

        $cek = $mscustomer->cekCustomer($request->input('custid'));


        $custname = $cek->CustName;

        if ($cek == false) {

            return $this->responseError('kode pelanggan tidak terdaftar dalam master', 400);
        }

        $cek = $mssales->cekSales($request->input('salesid'));

        if ($cek == false) {

            return $this->responseError('kode sales tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $insertheader = $sales->updateAllData([
                'saleid' => $request->input('saleid'),
                'poid' => $request->input('poid'),
                'transdate' => $request->input('transdate'),
                'custid' => $request->input('custid'),
                'salesid' => $request->input('salesid'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'fgtax' => $request->input('fgtax') ?? 'T',
                'nilaitax' => $request->input('nilaitax') ?? '',
                'fpsid' => $request->input('fpsid') ?? '',
                'note' => $request->input('note') ?? 0
            ]);

            if ($insertheader == false) {

                DB::rollBack();

                return $this->responseError('insert header gagal', 400);
            }

            $deletedetail = $modelDetail->deleteData([
                'saleid' => $request->input('saleid')
            ]);

            $deletallitem = $sales->deleteAllItem([
                'saleid' => $request->input('saleid')
            ]);

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {
                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode mobil tidak terdaftar dalam master', 400);
                }

                $insertdetail = $modelDetail->insertData([
                    'saleid' => $request->input('saleid'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'price' => $arrDetail[$i]['price'],
                    'qty' => $arrDetail[$i]['qty'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'note' => $arrDetail[$i]['note']
                ]);

                if ($insertdetail == false) {

                    DB::rollBack();

                    return $this->responseError('insert detail gagal', 400);
                }

                $insertallitem = $sales->insertAllItem([
                    'saleid' => $request->input('saleid'),
                    'transdate' => $request->input('transdate'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'price' => $arrDetail[$i]['price'],
                    'qty' => $arrDetail[$i]['qty'],
                    'custname' => $custname,

                ]);

                //dd(var_dump($insertheader));

                if ($insertallitem == false) {

                    DB::rollBack();

                    return $this->responseError('insert allitem gagal', 400);
                }

                if (isset($arrDetail[$i]['detailsn'])) {

                    $arrDetailsn = $arrDetail[$i]['detailsn'];

                    if (sizeof($arrDetailsn) <> $arrDetail[$i]['qty']) {

                        DB::rollBack();

                        return $this->responseError('jumlah No polisi mobil (kode :' . $arrDetail[$i]['itemid'] . ') tidak sama dengan jumlah detail', 400);
                    }

                    for ($u = 0; $u < sizeof($arrDetailsn); $u++) {

                        $insertdetail = $modelBiaya->insertData([
                            'saleid' => $request->input('saleid'),
                            'snid' => $arrDetailsn[$u]['snid'],
                            'itemid' => $arrDetail[$i]['itemid'],
                            'price' => $arrDetail[$i]['price'],
                            'modal' => $arrDetailsn[$u]['modal'],
                            'purchaseid' => $arrDetailsn[$u]['purchaseid'],
                            'fgsn' => $arrDetailsn[$u]['fgsn'],
                            'upduser' => Auth::user()->currentAccessToken()['namauser']
                        ]);

                        if ($insertdetail == false) {

                            DB::rollBack();

                            return $this->responseError('update Nopol gagal !', 400);
                        }
                    }
                }
            }

            $hitung = $sales->hitungTotal([
                'saleid' => $request->input('saleid')
            ]);

            $sales->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'ppn' => $hitung->ppn,
                'subtotal' => $hitung->subtotal,
                'saleid' => $request->input('saleid')
            ]);


            DB::commit();

            return $this->responseSuccess('update berhasil', 200, ['saleid' => $request->input('saleid')]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function getListData(Request $request)
    {
        $sales = new PenjualanHd();
        $salesdt = new PenjualanDt();

        if ($request->input('saleid')) {

            $resultheader = $sales->getdata(
                [
                    'saleid' => $request->input('saleid')
                ]
            );

            $resultdetail = $salesdt->getdata(
                [
                    'saleid' => $request->input('saleid')
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
                    'saleidkeyword' => $request->input('saleidkeyword') ?? '',
                    'poidkeyword' => $request->input('poidkeyword') ?? '',
                    'custidkeyword' => $request->input('custidkeyword') ?? '',
                    'custnamekeyword' => $request->input('custnamekeyword') ?? '',
                    'salesidkeyword' => $request->input('salesidkeyword') ?? '',
                    'salesnamekeyword' => $request->input('salesnamekeyword') ?? '',
                    'sortby' => $request->input('sortby') ?? 'old'
                ]
            );

            $resultPaginated = $this->arrayPaginator($request, $result);

            return $this->responsePagination($resultPaginated);
        }
    }

    public function deleteData(Request $request)
    {
        $sales = new PenjualanHd();

        $cek = $sales->cekPenjualan($request->input('saleid'));

        if ($cek == false) {

            return $this->responseError('nomor Penjualan tidak terdaftar', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $sales->deleteData([
                'saleid' => $request->input('saleid')
            ]);

            $deletallitem = $sales->deleteAllItem([
                'saleid' => $request->input('saleid')
            ]);

            if ($deleted) {
                DB::commit();

                return $this->responseSuccess('delete berhasil', 200, ['saleid' => $request->input('saleid')]);
            } else {
                DB::rollBack();

                return $this->responseError('delete gagal', 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function cariSN(Request $request)
    {
        $sales = new PenjualanSN();

        //$tanggal = $saleshd->cekSales($request->input('transdate'));

        $validatorBiaya = Validator::make($request->all(), $sales::$rulesInsert2, $sales::$messagesInsert2);

        if ($validatorBiaya->fails()) {
            return $this->responseError($validatorBiaya->messages(), 400);
        } else {

            $resultdetail = $sales->selectSN(
                [
                    'itemid' => $request->input('itemid'),
                    'suppidkeyword' => $request->input('suppidkeyword') ?? '',
                    'suppnamekeyword' => $request->input('suppnamekeyword') ?? '',
                    'purchaseidkeyword' => $request->input('purchaseidkeyword') ?? '',
                    'snidkeyword' => $request->input('snidkeyword') ?? ''
                ]
            );


            //return $this->responseData($resultdetail);

            $resultPaginated = $this->arrayPaginator($request, $resultdetail);

            return $this->responsePagination($resultPaginated);
        }
    }
}

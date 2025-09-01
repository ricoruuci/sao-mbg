<?php

namespace App\Http\Controllers\AR\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AR\Activity\SalesOrderHd;
use App\Models\AR\Activity\SalesOrderDt;
use App\Models\AR\Master\ARMsCustomer;
use App\Models\AR\Master\ARMsSales;
use App\Models\IN\Master\INMsItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    use ArrayPaginator, HttpResponse;

    public function insertData(Request $request)
    {

        $sales = new SalesOrderHd();

        $modelDetail = new SalesOrderDt();

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

        $cek = $mscustomer->cekCustomer($request->input('custid'));

        if ($cek == false) {

            return $this->responseError('kode pelanggan tidak terdaftar dalam master', 400);
        }

        $cek = $mssales->cekSales($request->input('salesid'));

        if ($cek == false) {

            return $this->responseError('kode sales tidak terdaftar dalam master', 400);
        }

        DB::beginTransaction();

        try {
            $hasilpoid = $sales->beforeAutoNumber($request->input('transdate'));

            $insertheader = $sales->insertData([
                'poid' => $hasilpoid,
                'prid' => $request->input('prid'),
                'custid' => $request->input('custid'),
                'transdate' => $request->input('transdate'),
                'note' => $request->input('note'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'tglkirim' => $request->input('tglkirim'),
                'salesid' => $request->input('salesid'),
                'currid' => $request->input('currid'),
                'fob' => $request->input('fob'),
                'ppn' => $request->input('ppn')
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
                    'itemid' => $arrDetail[$i]['itemid'],
                    'qty' => $arrDetail[$i]['qty'],
                    'price' => $arrDetail[$i]['price'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'itemname' => $arrDetail[$i]['itemname'],
                    'modal' => $arrDetail[$i]['modal'],
                    'bagasi' => $arrDetail[$i]['bagasi'],
                    'keterangan' => $arrDetail[$i]['keterangan']
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
                'soid' => $hasilpoid
            ]);

            $sales->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'soid' => $hasilpoid
            ]);

            $hasilotorisasi = $sales->cekOtorisasi([
                'soid' => $hasilpoid,
                'custid' => $request->input('custid'),
                'transdate' => $request->input('transdate')
            ]);

            if (is_null($hasilotorisasi)) {
                $flag = 'T';
            } else {
                $flag = $hasilotorisasi->flag;
            };

            $sales->updateJenis([
                'jenis' => $flag,
                'soid' => $hasilpoid,
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
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
        $sales = new SalesOrderHd();
        $salesdt = new SalesOrderDt();

        if ($request->input('soid')) {

            $resultheader = $sales->getdata(
                [
                    'poid' => $request->input('soid')
                ]
            );

            $resultdetail = $salesdt->getdata(
                [
                    'poid' => $request->input('soid')
                ]
            );

            $result = [
                'header' => $resultheader,
                'detail' => $resultdetail
            ];

            return $this->responseData($result);
        } else {
            if ($request->input('oto')) {

                $result = $sales->getListOto();

                $resultPaginated = $this->arrayPaginator($request, $result);

                return $this->responsePagination($resultPaginated);
            } else {
                $result = $sales->getListData(
                    [
                        'dari' => $request->input('dari'),
                        'sampai' => $request->input('sampai'),
                        'custidkeyword' => $request->input('custidkeyword') ?? '',
                        'soidkeyword' => $request->input('soidkeyword') ?? '',
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
    }

    public function getSOforPO(Request $request)
    {
        $sales = new SalesOrderHd();

        $result = $sales->getSOforPO(
            [
                'transdate' => $request->input('transdate'),
                'soidkeyword' => $request->input('soidkeyword') ?? '',
                'custnamekeyword' => $request->input('custnamekeyword') ?? '',
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function getItemSOforPO(Request $request)
    {
        $sales = new SalesOrderDt();

        $result = $sales->getItemSOforPO(
            [
                'soid' => $request->input('soid')
            ]
        );

        $resultPaginated = $this->arrayPaginator($request, $result);

        return $this->responsePagination($resultPaginated);
    }

    public function updateAllData(Request $request)
    {
        $sales = new SalesOrderHd();

        $modelDetail = new SalesOrderDt();

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

        $cek = $sales->cekSalesorder($request->input('soid'));

        if ($cek == false) {

            return $this->responseError('nomor sales order tidak terdaftar', 400);
        }

        $cek = $mscustomer->cekCustomer($request->input('custid'));

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
                'soid' => $request->input('soid'),
                'prid' => $request->input('prid'),
                'custid' => $request->input('custid'),
                'transdate' => $request->input('transdate'),
                'note' => $request->input('note'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
                'tglkirim' => $request->input('tglkirim'),
                'salesid' => $request->input('salesid'),
                'currid' => $request->input('currid'),
                'fob' => $request->input('fob'),
                'ppn' => $request->input('ppn')
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
                'soid' => $request->input('soid')
            ]);

            $arrDetail = $request->input('detail');

            for ($i = 0; $i < sizeof($arrDetail); $i++) {
                $cek = $msitem->cekBarang($arrDetail[$i]['itemid']);

                if ($cek == false) {

                    DB::rollBack();

                    return $this->responseError('kode barang tidak terdaftar dalam master', 400);
                }

                $insertdetail = $modelDetail->insertData([
                    'poid' => $request->input('soid'),
                    'itemid' => $arrDetail[$i]['itemid'],
                    'qty' => $arrDetail[$i]['qty'],
                    'price' => $arrDetail[$i]['price'],
                    'upduser' => Auth::user()->currentAccessToken()['namauser'],
                    'itemname' => $arrDetail[$i]['itemname'],
                    'modal' => $arrDetail[$i]['modal'],
                    'bagasi' => $arrDetail[$i]['bagasi'],
                    'keterangan' => $arrDetail[$i]['keterangan']
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
                'soid' => $request->input('soid')
            ]);

            $sales->updateTotal([
                'grandtotal' => $hitung->grandtotal,
                'soid' => $request->input('soid')
            ]);

            $hasilotorisasi = $sales->cekOtorisasi([
                'soid' => $request->input('soid'),
                'custid' => $request->input('custid'),
                'transdate' => $request->input('transdate')
            ]);

            if (is_null($hasilotorisasi)) {
                $flag = 'T';
            } else {
                $flag = $hasilotorisasi->flag;
            }

            $sales->updateJenis([
                'jenis' => $flag,
                'soid' => $request->input('soid'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
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

    public function deleteData(Request $request)
    {
        $sales = new SalesOrderHd();

        $cek = $sales->cekSalesorder($request->input('soid'));

        if ($cek == false) {

            return $this->responseError('nomor sales order tidak terdaftar', 400);
        }

        DB::beginTransaction();

        try {
            $deleted = $sales->deleteData([
                'soid' => $request->input('soid')
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

    public function updateJenis(Request $request)
    {
        $sales = new SalesOrderHd();

        $cek = $sales->cekSalesorder($request->input('soid'));

        if ($cek == false) {

            return $this->responseError('nomor sales order tidak terdaftar', 400);
        }

        DB::beginTransaction();

        try {
            $updated = $sales->updateJenis([
                'soid' => $request->input('soid'),
                'jenis' => $request->input('jenis'),
                'upduser' => Auth::user()->currentAccessToken()['namauser'],
            ]);

            if ($updated) {

                $result = [
                    'success' => true,
                    'updated' => $updated
                ];
            } else {
                DB::rollBack();

                return $this->responseError('gagal update', 400);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseError($e->getMessage(), 400);
        }
    }
}

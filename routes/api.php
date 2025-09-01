<?php

//Pembelian

use App\Http\Controllers\AP\Activity\OtorisasiPembelianController;
use App\Http\Controllers\AP\Activity\PembelianController;
use App\Http\Controllers\AP\Master\SupplierController;
use App\Http\Controllers\AP\Report\APReportController;
use App\Http\Controllers\AP\Activity\PurchaseOrderController;
use App\Http\Controllers\AR\Activity\PenjualanController;
//Penjualan
use App\Http\Controllers\AR\Master\CustomerController;
use App\Http\Controllers\AR\Master\SalesController;
use App\Http\Controllers\AR\Activity\SalesOrderController;
use App\Http\Controllers\AR\Report\ARReportController;
//Inventory
use App\Http\Controllers\IN\Master\SatuanController;
use App\Http\Controllers\IN\Master\ItemController;
use App\Http\Controllers\IN\Master\GroupController;
use App\Http\Controllers\IN\Master\ProductController;
use App\Http\Controllers\IN\Master\WarehouseController;
use App\Http\Controllers\IN\Report\INReportController;
//Cash Flow atau Keuangan
use App\Http\Controllers\CF\Master\BankController;
use App\Http\Controllers\CF\Master\RekeningController;
use App\Http\Controllers\CF\Activity\TxnKKBBController;
//Others
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CF\Master\GroupRekController;
use App\Http\Controllers\CF\Report\RptBukuBesarController;
use App\Http\Controllers\CF\Report\RptFinanceController;
use App\Http\Controllers\CF\Report\RptKKBBController;
use App\Http\Controllers\TestController;
use App\Models\AP\Activity\OtorisasiPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
//=====================================================================================================================
//                                                  ROUTE FOR TEST
//=====================================================================================================================
Route::get('test', [TestController::class, 'gettest']);
Route::post('test', [TestController::class, 'posttest']);
// Route::post('kaskeluar/{data?}', [TestController::class, 'posttest'])->defaults('data', 'KK');
//=====================================================================================================================
//                                                  DASHBOARD
//=====================================================================================================================
//Dashboard 
Route::get('dashboard', [DashboardController::class, 'getGrafikPenjualan'])->middleware('auth:sanctum');
Route::get('dashboard/tahun', [DashboardController::class, 'getSalesYear'])->middleware('auth:sanctum');
Route::get('dashboard/po', [DashboardController::class, 'getTotalPO'])->middleware('auth:sanctum');
Route::get('dashboard/so', [DashboardController::class, 'getTotalSO'])->middleware('auth:sanctum');
Route::get('dashboard/sales', [DashboardController::class, 'getTotalJual'])->middleware('auth:sanctum');
Route::get('dashboard/purchase', [DashboardController::class, 'getTotalBeli'])->middleware('auth:sanctum');
Route::get('dashboard/sopending', [DashboardController::class, 'getSoPending'])->middleware('auth:sanctum');
Route::get('dashboard/user', [DashboardController::class, 'getUserAktif'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                  SYSTEM
//=====================================================================================================================
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::patch('changepass', [AuthController::class, 'changePass'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                  PEMBELIAN
//=====================================================================================================================
Route::get('otorisasi/pembelian', [OtorisasiPembelianController::class, 'getListOto'])->middleware('auth:sanctum');
Route::patch('otorisasi/pembelian', [OtorisasiPembelianController::class, 'updateData'])->middleware('auth:sanctum');

//=====================================================================================================================
//                                                    MASTER
//=====================================================================================================================
//Master Supplier
Route::post('supplier', [SupplierController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('supplier', [SupplierController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('supplier/{suppid}', [SupplierController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('supplier/{suppid}', [SupplierController::class, 'deleteData'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                   TRANSAKSI
//=====================================================================================================================

//purchase order 
Route::post('purchaseorder', [PurchaseOrderController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('purchaseorder', [PurchaseOrderController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('purchaseorder', [PurchaseOrderController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('purchaseorder', [PurchaseOrderController::class, 'deleteData'])->middleware('auth:sanctum');


//Pembelian 
Route::post('pembelian', [PembelianController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('pembelian', [PembelianController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('pembelian', [PembelianController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('pembelian', [PembelianController::class, 'deleteData'])->middleware('auth:sanctum');


//=====================================================================================================================
//                                                    LAPORAN
//=====================================================================================================================
Route::get('rpthutang', [APReportController::class, 'getRptHutang'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                  PENJUALAN
//=====================================================================================================================
//=====================================================================================================================
//                                                    MASTER
//=====================================================================================================================
//Master Customer
Route::post('customer', [CustomerController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('customer', [CustomerController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('customer/{custid}', [CustomerController::class, 'getData'])->middleware('auth:sanctum');
Route::patch('customer/{custid}', [CustomerController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('customer/{custid}', [CustomerController::class, 'deleteData'])->middleware('auth:sanctum');
//Master Sales
Route::post('sales', [SalesController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('sales', [SalesController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('sales/{salesid}', [SalesController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('sales/{salesid}', [SalesController::class, 'deleteData'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                   TRANSAKSI
//=====================================================================================================================
//Sales Order 
Route::post('salesorder', [SalesOrderController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('salesorder', [SalesOrderController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('salesorder', [SalesOrderController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('salesorder', [SalesOrderController::class, 'deleteData'])->middleware('auth:sanctum');
Route::patch('otorisasiso', [SalesOrderController::class, 'updateJenis'])->middleware('auth:sanctum');
Route::get('listsoblmpo', [SalesOrderController::class, 'getSOforPO'])->middleware('auth:sanctum');
Route::get('itemso', [SalesOrderController::class, 'getItemSOforPO'])->middleware('auth:sanctum');

//Penjualan 
Route::post('penjualan', [PenjualanController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('penjualan', [PenjualanController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('penjualan', [PenjualanController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('penjualan', [PenjualanController::class, 'deleteData'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                    LAPORAN
//=====================================================================================================================
Route::get('rptpiutang', [ARReportController::class, 'getRptPiutang'])->middleware('auth:sanctum');
Route::get('rekappenjualan', [ARReportController::class, 'getRptPenjualan'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                  INVENTORY
//=====================================================================================================================
//=====================================================================================================================
//                                                    MASTER
//=====================================================================================================================
//Master Barang
Route::post('item', [ItemController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('item', [ItemController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('item', [ItemController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('item', [ItemController::class, 'deleteData'])->middleware('auth:sanctum');
//Master Satuan
Route::get('uomid', [SatuanController::class, 'getListData'])->middleware('auth:sanctum');
//Master Group
Route::post('group', [GroupController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('group', [GroupController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('group', [GroupController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('group', [GroupController::class, 'deleteData'])->middleware('auth:sanctum');
//Master Product
Route::post('product', [ProductController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('product', [ProductController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('product', [ProductController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('product', [ProductController::class, 'deleteData'])->middleware('auth:sanctum');
//Master Warehouse
Route::get('warehouse', [WarehouseController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('warehouse/{warehouseid}', [WarehouseController::class, 'getData'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                   TRANSAKSI
//=====================================================================================================================
//=====================================================================================================================
//                                                    LAPORAN
//=====================================================================================================================
Route::get('rptstock', [INReportController::class, 'getRptStock'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                   FINANCE
//=====================================================================================================================
//=====================================================================================================================
//                                                    MASTER
//=====================================================================================================================
//Master Bank
Route::get('bank', [BankController::class, 'getListData'])->middleware('auth:sanctum');
//Master Rekening
Route::get('rekening', [RekeningController::class, 'getListData'])->middleware('auth:sanctum');
Route::post('rekening', [RekeningController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('rekening', [RekeningController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('rekening', [RekeningController::class, 'deleteData'])->middleware('auth:sanctum');


//Group Rekening
Route::post('grouprek', [GroupRekController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('grouprek', [GroupRekController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('grouprek', [GroupRekController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('grouprek', [GroupRekController::class, 'deleteData'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                   TRANSAKSI
//=====================================================================================================================
//TransaksiHd
Route::post('txnkkbb', [TxnKKBBController::class, 'insertData'])->middleware('auth:sanctum');
Route::get('txnkkbb', [TxnKKBBController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('txnkkbb', [TxnKKBBController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('txnkkbb', [TxnKKBBController::class, 'deleteData'])->middleware('auth:sanctum');
Route::get('txnkkbb/carinota', [TxnKKBBController::class, 'cariNota'])->middleware('auth:sanctum');
//=====================================================================================================================
//                                                    LAPORAN
//=====================================================================================================================

Route::get('rptbukubesar', [RptFinanceController::class, 'getRptBukuBesar'])->middleware('auth:sanctum');
Route::get('rptlabarugi', [RptFinanceController::class, 'getRptLabaRugi'])->middleware('auth:sanctum');
Route::get('rptneraca', [RptFinanceController::class, 'getRptNeraca'])->middleware('auth:sanctum');

Route::get('rptkas', [RptKKBBController::class, 'getLaporanKas'])->middleware('auth:sanctum');
Route::get('rptbank', [RptKKBBController::class, 'getLaporanBank'])->middleware('auth:sanctum');

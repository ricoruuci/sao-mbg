<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\JualController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\GroupBahanBakuController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TxnKKBBController;
use App\Http\Controllers\GroupRekeningController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\BeliController;
use App\Http\Controllers\RptPenjualanController;
use App\Http\Controllers\RptPembelianController;
use App\Http\Controllers\SetRekeningController;
use App\Http\Controllers\RptFinanceController;
use App\Http\Controllers\RptInventoryController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\UserController;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('list-cabang', [CabangController::class, 'getListData'])->middleware('auth:sanctum');

Route::get('user', [UserController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('user-by-id', [UserController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('user', [UserController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('user', [UserController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('user', [UserController::class, 'deleteData'])->middleware('auth:sanctum');
Route::patch('change-password', [UserController::class, 'updatePassword'])->middleware('auth:sanctum');

// Route::get('waiter', [WaiterController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('menu-penjualan', [MenuController::class, 'getListMenuPenjualan'])->middleware('auth:sanctum');
Route::get('meja', [MejaController::class, 'getListData'])->middleware('auth:sanctum');

Route::get('bank', [BankController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('bank-by-id', [BankController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('bank', [BankController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('bank', [BankController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('bank', [BankController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('menu', [MenuController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('menu-by-id', [MenuController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('menu', [MenuController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('menu', [MenuController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('menu', [MenuController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('group-menu', [GroupMenuController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('group-menu-by-id', [GroupMenuController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('group-menu', [GroupMenuController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('group-menu', [GroupMenuController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('group-menu', [GroupMenuController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('satuan', [SatuanController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('satuan-by-id', [SatuanController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('satuan', [SatuanController::class, 'insertData'])->middleware('auth:sanctum');
Route::delete('satuan', [SatuanController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('bahan-baku', [BahanBakuController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('bahan-baku-by-id', [BahanBakuController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('bahan-baku', [BahanBakuController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('bahan-baku', [BahanBakuController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('bahan-baku', [BahanBakuController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('group-bahan-baku', [GroupBahanBakuController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('group-bahan-baku-by-id', [GroupBahanBakuController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('group-bahan-baku', [GroupBahanBakuController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('group-bahan-baku', [GroupBahanBakuController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('group-bahan-baku', [GroupBahanBakuController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('supplier', [SupplierController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('supplier-by-id', [SupplierController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('supplier', [SupplierController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('supplier', [SupplierController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('supplier', [SupplierController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('penjualan', [JualController::class, 'getListData'])->middleware('auth:sanctum');
Route::post('penjualan', [JualController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('penjualan', [JualController::class, 'updateData'])->middleware('auth:sanctum');
Route::patch('batal-penjualan', [JualController::class, 'updateBatal'])->middleware('auth:sanctum');

Route::get('data-meja', [JualController::class, 'getDataMeja'])->middleware('auth:sanctum');
Route::patch('payment', [JualController::class, 'updatePayment'])->middleware('auth:sanctum');

Route::get('txnkkbb', [TxnKKBBController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('txnkkbb-by-id', [TxnKKBBController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('txnkkbb', [TxnKKBBController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('txnkkbb', [TxnKKBBController::class, 'updateAllData'])->middleware('auth:sanctum');
Route::delete('txnkkbb', [TxnKKBBController::class, 'deleteData'])->middleware('auth:sanctum');
Route::get('cari-nota-belum-lunas', [TxnKKBBController::class, 'cariNota'])->middleware('auth:sanctum');

Route::get('group-rekening', [GroupRekeningController::class, 'getListData'])->middleware('auth:sanctum');

Route::get('rekening', [RekeningController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('rekening-by-id', [RekeningController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('rekening', [RekeningController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('rekening', [RekeningController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('rekening', [RekeningController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('beli', [BeliController::class, 'getListData'])->middleware('auth:sanctum');
Route::get('beli-by-id', [BeliController::class, 'getDataById'])->middleware('auth:sanctum');
Route::post('beli', [BeliController::class, 'insertData'])->middleware('auth:sanctum');
Route::patch('beli', [BeliController::class, 'updateData'])->middleware('auth:sanctum');
Route::delete('beli', [BeliController::class, 'deleteData'])->middleware('auth:sanctum');

Route::get('set-rekening', [SetRekeningController::class, 'getListData'])->middleware('auth:sanctum');
Route::patch('set-rekening', [SetRekeningController::class, 'updateData'])->middleware('auth:sanctum');

/*==============================================================================================================
 * Laporan 
 *==============================================================================================================*/

Route::get('rpt-penjualan', [RptPenjualanController::class, 'getLapPenjualan'])->middleware('auth:sanctum');
Route::get('rpt-penjualan-harian', [RptPenjualanController::class, 'getLapPenjualanHarian'])->middleware('auth:sanctum');
Route::get('rpt-pembelian', [RptPembelianController::class, 'getLapPembelian'])->middleware('auth:sanctum');
Route::get('rpt-hutang', [RptPembelianController::class, 'getLapHutang'])->middleware('auth:sanctum');

Route::get('rpt-buku-besar', [RptFinanceController::class, 'getRptBukuBesar'])->middleware('auth:sanctum');
Route::get('rpt-laba-rugi', [RptFinanceController::class, 'getRptLabaRugi'])->middleware('auth:sanctum');
Route::get('rpt-neraca', [RptFinanceController::class, 'getRptNeraca'])->middleware('auth:sanctum');

Route::get('rpt-stock-akhir', [RptInventoryController::class, 'getLapStock'])->middleware('auth:sanctum');
Route::get('rpt-kartu-stock', [RptInventoryController::class, 'getLapKartuStock'])->middleware('auth:sanctum');


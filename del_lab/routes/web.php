<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - DEL-LEATS (DEL Lab Equipment & Asset Tracking System)
|--------------------------------------------------------------------------
*/

// Redirect root ke katalog aset
Route::get('/', function () {
    return redirect()->route('equipments.index');
});

// 1. GET  /equipments        -> EquipmentController@index (Katalog berpaginasi, pencarian & filter)
// 2. GET  /equipments/create -> EquipmentController@create (Tampilan formulir tambah aset)
// 3. POST /equipments        -> EquipmentController@store (Validasi, upload foto, penyimpanan aset)
// 4. GET  /equipments/{id}   -> EquipmentController@show (Detail spesifikasi & ketersediaan aset)
Route::resource('equipments', EquipmentController::class)->only([
    'index', 'create', 'store', 'show'
]);

// 5. GET  /loans/create      -> LoanController@create (Formulir pengajuan peminjaman aset)
// 6. POST /loans             -> LoanController@store (Validasi aturan bisnis & transaksi peminjaman)
// Extra: GET /loans          -> LoanController@index (Riwayat Peminjaman)
Route::resource('loans', LoanController::class)->only([
    'index', 'create', 'store'
]);

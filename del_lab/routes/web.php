<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('equipments.index');
});

Route::resource('equipments', EquipmentController::class)->only([
    'index', 'create', 'store', 'show'
]);

Route::resource('loans', LoanController::class)->only([
    'index', 'create', 'store'
]);

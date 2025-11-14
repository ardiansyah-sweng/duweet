<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/cashout/sum', [ReportController::class, 'showCashoutSumForm'])
        ->name('admin.cashout.sum.form');

    Route::post('/cashout/sum', [ReportController::class, 'getCashoutSumByPeriod'])
        ->name('admin.cashout.sum.result');
        
    Route::post('/cashout/sum/export', [ReportController::class, 'exportCashoutCsv'])
        ->name('admin.cashout.sum.export');
});

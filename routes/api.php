<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialAccountController;

Route::prefix('financial-account')->group(function () {

    // 1. Default: Hanya is_active = true, atau filter sesuai query param ?is_active=...
    Route::get('/', [FinancialAccountController::class, 'index'])
        ->name('api.financial-account.index');

    // 2. Khusus untuk Active: Selalu mengembalikan data aktif
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts']) // <-- Ganti method ke getActiveAccounts
        ->name('api.financial-account.active');

    // 3. Route untuk show by ID
    Route::get('/{id}', [FinancialAccountController::class, 'show'])
        ->name('api.financial-account.show');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\FinancialAccountController;

Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
});

Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveFinancialAccounts'])->name('api.financial-account.active');
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\FinancialAccountController;

// Monthly expenses
Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);

// UserAccount API Routes (no CSRF protection needed)
// tambahkan route UserAccount sesuai kebutuhan, contoh:
Route::get('/user-accounts', [UserAccountController::class, 'index']);
Route::get('/user-accounts/{id}', [UserAccountController::class, 'show']);
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
});

Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

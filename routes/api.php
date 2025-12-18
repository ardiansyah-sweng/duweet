<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;

// UserAccount API Routes (no CSRF protection needed)
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
});

// Transaction API Routes
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('api.transactions.index');
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod'])->name('api.transactions.filter-period');
    Route::get('/filter/month', [TransactionController::class, 'filterByMonth'])->name('api.transactions.filter-month');
    Route::get('/filter/year', [TransactionController::class, 'filterByYear'])->name('api.transactions.filter-year');
});

Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

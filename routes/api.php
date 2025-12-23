<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;

// UserAccount API
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::get('/{id}', [UserAccountController::class, 'show']);
    Route::post('/', [UserAccountController::class, 'store']);
    Route::put('/{id}', [UserAccountController::class, 'update']);
    Route::delete('/{id}', [UserAccountController::class, 'destroy']);
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw']);
});

// Transaction API
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/{id}', [TransactionController::class, 'show']);
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod']);
});

// Financial Account API
Route::prefix('financial-account')->group(function () {
<<<<<<< HEAD
    Route::get('/{id}', [FinancialAccountController::class, 'show']);
=======
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts'])->name('api.financial-account.active');
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
>>>>>>> 2e6795b85b7c600fcd326f22537e737dd96beb55
});

// Reports API
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount']);
    Route::get('/surplus-deficit/{userId}', [ReportController::class, 'getUserSurplusDeficit']);
});

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;

// ================= TRANSACTIONS =================
Route::get('/transactions/{id}', [TransactionController::class, 'show']);

// -------- ACCOUNT AGGREGATIONS --------
Route::get('/total-per-user', [AccountController::class, 'apiTotalPerUser']);
Route::get('/users/account-counts', [AccountController::class, 'apiAccountCountPerUser']);
Route::get('/user/{id}/account-count', [AccountController::class, 'apiAccountCountForUser']);
Route::get('/user/{id}/accounts', [AccountController::class, 'apiAccountsForUser']);
Route::get('/users/totals-with-counts', [AccountController::class, 'apiTotalsWithCounts']);

// -------- USER ACCOUNT --------
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::get('/{id}', [UserAccountController::class, 'show']);
    Route::post('/', [UserAccountController::class, 'store']);
    Route::put('/{id}', [UserAccountController::class, 'update']);
    Route::delete('/{id}', [UserAccountController::class, 'destroy']);
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw']);
});

// -------- TRANSACTIONS --------
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod']);
});

// -------- FINANCIAL ACCOUNT --------
Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show']);
});

// -------- REPORTS --------
Route::prefix('reports')->group(function () {
    Route::get(
        '/transactions-per-user-account',
        [ReportController::class, 'getTotalTransactionsPerUserAccount']
    );
});

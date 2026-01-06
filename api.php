<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialAccountController;

/*
|--------------------------------------------------------------------------
| HEALTH CHECK
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json(['status' => 'OK']));
Route::get('/ping', fn () => response()->json(['pong' => true]));

/*
|--------------------------------------------------------------------------
| USER ACCOUNT
|--------------------------------------------------------------------------
*/
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::get('/{id}', [UserAccountController::class, 'show']);
    Route::get('/find-by-id/{id}', [UserAccountController::class, 'findById']);

    Route::post('/', [UserAccountController::class, 'store']);
    Route::put('/{id}', [UserAccountController::class, 'update']);

    Route::delete('/{id}', [UserAccountController::class, 'destroy']);
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw']);

    Route::post('/login', [UserAccountController::class, 'login']);
});

Route::post('/reset-password', [UserAccountController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/
Route::post('/users', [UserController::class, 'createUserRaw']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| TRANSACTIONS
|--------------------------------------------------------------------------
*/
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/{id}', [TransactionController::class, 'show']);
    Route::get('/monthly-expense', [TransactionController::class, 'monthlyExpense']);
});

/*
|--------------------------------------------------------------------------
| FINANCIAL ACCOUNT
|--------------------------------------------------------------------------
*/
Route::get('/financial_accounts', [AccountController::class, 'index']);
Route::post('/financial_accounts', [AccountController::class, 'store']);
Route::get('/financial_accounts/{id}', [AccountController::class, 'show']);

Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts']);
    Route::get('/{id}', [FinancialAccountController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| REPORT
|--------------------------------------------------------------------------
*/
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount']);
    Route::get('/surplus-defisit', [ReportController::class, 'surplusDefisitByPeriod']);
});

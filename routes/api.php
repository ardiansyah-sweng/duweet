<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Account Nested
Route::get('/accounts/nested', [AccountController::class, 'index']);
Route::get('/accounts/nested/{id}', [AccountController::class, 'show']);

// Transaction
Route::get('/transactions/{id}', [TransactionController::class, 'show']);

// UserAccount API
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::get('/{id}', [UserAccountController::class, 'show']);
    Route::post('/', [UserAccountController::class, 'store']);
    Route::put('/{id}', [UserAccountController::class, 'update']);
    Route::delete('/{id}', [UserAccountController::class, 'destroy']);
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw']);
});

// Financial Account
Route::get('/financial-account/{id}', [FinancialAccountController::class, 'show']);

// Reports
Route::get('/reports/transactions-per-user-account', 
    [ReportController::class, 'getTotalTransactionsPerUserAccount']
);

<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Models\FinancialAccount;
use Illuminate\Http\Request as HttpRequest;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;

// Monthly expenses
Route::get('/transactions/monthly-expense', [TransactionController::class, 'monthlyExpense']);


// Transaction detail
// Simple health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});
// jika ingin validasi cari user by email menggunakan POST
// Route::post('/find-by-email', [UserAccountController::class, 'findByEmail']);
Route::post('/reset-password', [UserAccountController::class, 'resetPassword']);

// GET endpoint to find user by email (safe response, no password)
Route::get('/user/find', [\App\Http\Controllers\UserAccountController::class, 'findByEmail']);

Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/transactions/{id}', [TransactionController::class, 'show']);


// UserAccount API Routes (no CSRF protection needed)
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

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::get('/accounts', function () {
    return response()->json(['ok' => true]);
});


Route::post('/financial_accounts', [AccountController::class, 'store']);
Route::get('/financial_accounts', [AccountController::class, 'index']);
Route::get('/financial_accounts/{id}', [AccountController::class, 'show']);

// Transaction API Routes
Route::prefix('transactions')->group(function () {
    // Main CRUD routes
    Route::get('/', [TransactionController::class, 'index'])->name('api.transactions.index');
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod'])->name('api.transactions.filter-period');
    Route::get('/{id}', [TransactionController::class, 'show'])->name('api.transactions.show');
    Route::post('/', [TransactionController::class, 'store'])->name('api.transactions.store');
    Route::put('/{id}', [TransactionController::class, 'update'])->name('api.transactions.update');
    Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('api.transactions.destroy');
    
    // Filter routes (dedicated endpoints)
    Route::get('/user-account/{userAccountId}', [TransactionController::class, 'getByUserAccount'])
        ->name('api.transactions.by-user-account');
    Route::get('/financial-account/{financialAccountId}', [TransactionController::class, 'getByFinancialAccount'])
        ->name('api.transactions.by-financial-account');
    Route::get('/group/{groupId}', [TransactionController::class, 'getByTransactionGroup'])
        ->name('api.transactions.by-group');
    
    // Statistics
    Route::get('/stats/user-account/{userAccountId}', [TransactionController::class, 'getStatsByUserAccount'])
        ->name('api.transactions.stats-by-user-account');
});

// Financial Account API Routes
Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts'])->name('api.financial-account.active');
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

// Reports API Routes
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount'])
        ->name('api.reports.transactions-per-user-account');
});

Route::get('/getLatestActivities', [TransactionController::class, 'getLatestActivities']);


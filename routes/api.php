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

// User API Routes (untuk registrasi dan pencarian berdasarkan nama/email/alamat)
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('api.user.index');
    Route::get('/search', [UserController::class, 'search'])->name('api.user.search');
    Route::post('/', [UserController::class, 'store'])->name('api.user.store');
    Route::get('/{id}', [UserController::class, 'show'])->name('api.user.show');
    Route::put('/{id}', [UserController::class, 'update'])->name('api.user.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('api.user.destroy');
});
// User API Routes
Route::post('/users', [UserController::class, 'createUserRaw']);
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
    Route::get('/', [TransactionController::class, 'index'])->name('api.transactions.index');
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod'])->name('api.transactions.filter-period');
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


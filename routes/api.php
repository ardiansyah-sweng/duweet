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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// =============================================================
// 1. USER ACCOUNT (Prioritas versi Kamu: ada storeRaw & destroyRaw)
// =============================================================
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
    // List semua user account
    Route::get('/', [UserAccountController::class, 'index'])
        ->name('api.user-account.index');

    // Detail by ID
    Route::get('/{id}', [UserAccountController::class, 'show'])
        ->whereNumber('id')
        ->name('api.user-account.show');

    // CREATE – cuma 1 endpoint, pakai RAW QUERY BUILDER
    Route::post('/', [UserAccountController::class, 'storeRaw'])
        ->name('api.user-account.store');

    // UPDATE
    Route::put('/{id}', [UserAccountController::class, 'update'])
        ->whereNumber('id')
        ->name('api.user-account.update');

    // DELETE pakai Eloquent
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])
        ->whereNumber('id')
        ->name('api.user-account.destroy');

    // DELETE pakai RAW QUERY (kalau masih mau dipakai tugas raw)
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])
        ->whereNumber('id')
        ->name('api.user-account.destroy-raw');
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
    Route::delete('/group/{groupId}/hard', [TransactionController::class, 'hardDeleteByGroupId']);
});

// Financial Account API Routes
Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts'])->name('api.financial-account.active');
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

// =============================================================
// 3. REPORTS
// =============================================================
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount'])
        ->name('api.reports.transactions-per-user-account');
});

// =============================================================
// 4. TRANSACTION (Tambahan dari Incoming Change)
// =============================================================
Route::get('/transactions/{id}', [TransactionController::class, 'show']);
Route::get('/getLatestActivities', [TransactionController::class, 'getLatestActivities']);


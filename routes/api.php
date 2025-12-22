<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Pastikan semua controller di-import
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController; // <--- Tambahan dari incoming change


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// =============================================================
// 1. USER ACCOUNT (Prioritas versi Kamu: ada storeRaw & destroyRaw)
// =============================================================
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

// Transaction API Routes
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('api.transactions.index');
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod'])->name('api.transactions.filter-period');
});

// Financial Account API Routes
Route::prefix('financial-account')->group(function () {
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
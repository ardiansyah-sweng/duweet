<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
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
Route::get('/transactions/{id}', [TransactionController::class, 'show']);

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
});

// Financial Account API Routes
Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

// Reports API Routes
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount'])
        ->name('api.reports.transactions-per-user-account');
});

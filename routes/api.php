<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\AdminController;

// UserAccount API Routes (no CSRF protection needed)
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
});

// Admin API Routes - Income Reports
Route::prefix('admin')->group(function () {
    Route::prefix('income')->group(function () {
        // Sum income by period (daily, weekly, monthly, yearly)
        Route::get('/by-period', [AdminController::class, 'getIncomeByPeriod'])->name('api.admin.income.by-period');
        
        // Sum income by financial account category
        Route::get('/by-category', [AdminController::class, 'getIncomeByCategory'])->name('api.admin.income.by-category');
        
        // Get income summary
        Route::get('/summary', [AdminController::class, 'getIncomeSummary'])->name('api.admin.income.summary');
        
        // Get comprehensive income report
        Route::get('/report', [AdminController::class, 'getIncomeReport'])->name('api.admin.income.report');
    });
});

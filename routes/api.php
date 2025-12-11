<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\ReportController;

// ===============================
// USER ACCOUNT API
// ===============================
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
});

// ===============================
// REPORT API ROUTES
// ===============================
Route::prefix('report')->group(function () {

    // Income summary for user
    Route::get('/income-summary', [ReportController::class, 'incomeSummary'])
        ->name('api.report.income-summary');

    // Surplus-defisit for admin
    Route::get('/surplus-defisit', [ReportController::class, 'surplusDefisitAdmin'])
        ->name('api.report.surplus-defisit');

});

<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\FinancialAccountController;

// UserAccount API Routes (no CSRF protection needed)
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::post('/', [UserAccountController::class, 'store']);
    Route::get('without-setup', [UserAccountController::class, 'usersWithoutSetupAccount']);
    Route::get('{id}', [UserAccountController::class, 'show']);
    Route::put('{id}', [UserAccountController::class, 'update']);
    Route::delete('{id}', [UserAccountController::class, 'destroy']);
});

Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

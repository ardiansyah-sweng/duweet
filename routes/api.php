<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\UserAccountController;

// Basic health check
Route::get('/ping', fn () => response()->json(['pong' => true]));

// Report endpoints
Route::get('/report/admin/liquid-assets-per-user', [ReportController::class, 'adminLiquidAssetsPerUser']);
Route::get('/report/user/{id}/liquid-assets', [ReportController::class, 'userLiquidAsset']);

// Financial Account endpoints
Route::get('/financial-account/{id}', [FinancialAccountController::class, 'show']);
Route::put('/financial-account/{id}/balance', [FinancialAccountController::class, 'updateBalance']);

// UserAccount API Routes (no CSRF protection needed)
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
});

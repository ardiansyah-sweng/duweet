<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReportController;
use App\Models\FinancialAccount;
use Illuminate\Http\Request as HttpRequest;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;

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

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::get('/accounts', function () {
    return response()->json(['ok' => true]);
});


Route::post('/financial_accounts', [AccountController::class, 'store']);
Route::get('/financial_accounts', [AccountController::class, 'index']);
Route::get('/financial_accounts/{id}', [AccountController::class, 'show']);

Route::get('/report/liquid-asset/{id}', [ReportController::class, 'userLiquidAsset']);
Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

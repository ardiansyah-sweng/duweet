<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\FinancialAccountController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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

Route::prefix('financial-account')->group(function () {
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
});

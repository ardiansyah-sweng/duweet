<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['status' => 'success', 'message' => 'API is running']);
});

Route::get('/accounts', function () {
    return response()->json(['status' => 'success', 'message' => 'Accounts endpoint']);
});

Route::get('/without-accounts', [ReportController::class, 'usersWithoutAccounts']);
Route::get('/without-active-accounts', [ReportController::class, 'usersWithoutActiveAccounts']);
Route::get('/{id}/liquid-assets', [ReportController::class, 'userLiquidAsset']);
Route::get('/income-summary', [ReportController::class, 'incomeSummary']);

Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::post('/', [UserAccountController::class, 'store']);
    Route::get('{id}', [UserAccountController::class, 'show']);
    Route::put('{id}', [UserAccountController::class, 'update']);
    Route::delete('{id}', [UserAccountController::class, 'destroy']);
});

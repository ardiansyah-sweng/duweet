<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionReportController;

Route::middleware('api')->group(function () {
    // Transaction Report Endpoints
    Route::prefix('reports/transactions')->group(function () {
        Route::get('per-user', [TransactionReportController::class, 'getTotalTransactionPerUser']);
    });
});


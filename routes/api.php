<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

// API routes (included from routes/web.php)
Route::prefix('api')->group(function () {
    // Controller-based API endpoint (uses configured DB connection via controller)
    Route::get('/total-per-user', [AccountController::class, 'apiTotalPerUser']);

    // Count of accounts per user (all users)
    Route::get('/users/account-counts', [AccountController::class, 'apiAccountCountPerUser']);

    // Count of accounts for a specific user
    Route::get('/user/{id}/account-count', [AccountController::class, 'apiAccountCountForUser']);

    // Return accounts for a specific user (includes total_balance per account)
    Route::get('/user/{id}/accounts', [AccountController::class, 'apiAccountsForUser']);

    // Return totals per user with account counts (total_balance + account_count)
    Route::get('/users/totals-with-counts', [AccountController::class, 'apiTotalsWithCounts']);
});

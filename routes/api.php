<?php

<<<<<<< HEAD
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
=======
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAccountController;

// UserAccount API Routes (no CSRF protection needed)
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
>>>>>>> 704974a8edd2f12696008b0f7dd219ec55e5e922
});

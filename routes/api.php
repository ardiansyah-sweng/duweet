<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserAccountController;


// API routes (included from routes/web.php)
Route::prefix('api')->group(function () {
    Route::get('/total-per-user', [AccountController::class, 'apiTotalPerUser']);
    Route::get('/users/account-counts', [AccountController::class, 'apiAccountCountPerUser']);
    Route::get('/user/{id}/account-count', [AccountController::class, 'apiAccountCountForUser']);
    Route::get('/user/{id}/accounts', [AccountController::class, 'apiAccountsForUser']);
    Route::get('/users/totals-with-counts', [AccountController::class, 'apiTotalsWithCounts']);


// UserAccount API Routes (no CSRF protection needed)
Route::prefix('user-account')->group(function () {
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
    Route::put('/{id}', [UserAccountController::class, 'update'])->name('api.user-account.update');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->name('api.user-account.destroy');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->name('api.user-account.destroy-raw');
    });

});
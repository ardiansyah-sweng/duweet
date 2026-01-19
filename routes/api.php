<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| HEALTH
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'timestamp' => now()->toDateTimeString()
]));

Route::get('/ping', fn () => response()->json(['pong' => true]));

/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
*/
Route::post('/users', [UserController::class, 'createUserRaw']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/users/{id}/accounts', [UserController::class, 'getUserAccounts'])
    ->whereNumber('id');

/*
|--------------------------------------------------------------------------
| USER ACCOUNT
|--------------------------------------------------------------------------
*/
Route::prefix('user-accounts')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::get('/{id}', [UserAccountController::class, 'show'])->whereNumber('id');
    Route::get('/find/{id}', [UserAccountController::class, 'findById'])->whereNumber('id');
    Route::post('/', [UserAccountController::class, 'store']);
    Route::put('/{id}', [UserAccountController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [UserAccountController::class, 'destroy'])->whereNumber('id');
    Route::delete('/{id}/raw', [UserAccountController::class, 'destroyRaw'])->whereNumber('id');
});

Route::get('/user/find', [UserAccountController::class, 'findByEmail']);
Route::post('/reset-password', [UserAccountController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| TRANSACTIONS
|--------------------------------------------------------------------------
*/
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/{id}', [TransactionController::class, 'show'])->whereNumber('id');

    Route::get('/by-user-account', [TransactionController::class, 'byUserAccount']);
    Route::get('/filter/period', [TransactionController::class, 'filterByPeriod']);
    Route::get('/monthly-expense', [TransactionController::class, 'monthlyExpense']);
    Route::get('/latest', [TransactionController::class, 'getLatestActivities']);

    Route::post('/', [TransactionController::class, 'Insert']);
    Route::delete('/group/{groupId}/hard', [TransactionController::class, 'hardDeleteByGroupId']);
});

/*
|--------------------------------------------------------------------------
| FINANCIAL ACCOUNTS
|--------------------------------------------------------------------------
*/
Route::prefix('financial-accounts')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store']);
    Route::get('/{id}', [AccountController::class, 'show'])->whereNumber('id');
});

Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts']);
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->whereNumber('id');

    Route::get('/liquid-assets/{user_account_id}', [FinancialAccountController::class, 'getUserLiquidAssets']);
    Route::get('/liquid-assets/all-users', [FinancialAccountController::class, 'getAllUsersLiquidAssets']);
    Route::get('/liquid-assets/admin/summary', [FinancialAccountController::class, 'adminLiquidAssetsSummary']);
});

/*
|--------------------------------------------------------------------------
| REPORTS
|--------------------------------------------------------------------------
*/
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount']);
    Route::get('/surplus-deficit', [ReportController::class, 'surplusDeficitByPeriod']);
    Route::get('/sum-by-type', [ReportController::class, 'sumFinancialAccountsByType']);
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin/reports')->group(function () {
    Route::get('/spending-summary', [ReportController::class, 'adminSpendingSummary']);
    Route::get('/cashin-by-period', [ReportController::class, 'adminCashinByPeriod']);
});

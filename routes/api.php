<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Models\FinancialAccount;
use Illuminate\Http\Request as HttpRequest;
// Explicit FQCN below for TransactionController to avoid analyzer confusion
use App\Http\Controllers\FinancialAccountController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// =============================================================
// 1. USER ACCOUNT (Prioritas versi Kamu: ada storeRaw & destroyRaw)
// =============================================================
// User API Routes
Route::post('/users', [UserController::class, 'createUserRaw']);
// Monthly expenses
Route::get('/transactions/monthly-expense', [\App\Http\Controllers\TransactionController::class, 'monthlyExpense']);


// Transaction detail
// Simple health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});
// jika ingin validasi cari user by email menggunakan POST
// Route::post('/find-by-email', [UserAccountController::class, 'findByEmail']);
Route::post('/reset-password', [UserAccountController::class, 'resetPassword']);

// GET endpoint to find user by email (safe response, no password)
Route::get('/user/find', [\App\Http\Controllers\UserAccountController::class, 'findByEmail']);

Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/transactions/{id}', [\App\Http\Controllers\TransactionController::class, 'show'])->whereNumber('id');


// UserAccount API Routes (no CSRF protection needed)
Route::get('/user-accounts', [UserAccountController::class, 'index']);
Route::get('/user-accounts/{id}', [UserAccountController::class, 'show']);

Route::prefix('user-account')->group(function () {
     Route::get('/inactive-users', [UserAccountController::class, 'inactiveByPeriod'])->name('api.user-account.inactive-users');
      Route::get('/not-logged-in/{days?}', [UserAccountController::class, 'notLoggedIn'])->name('api.user-account.not-logged-in');
    Route::get('/', [UserAccountController::class, 'index'])->name('api.user-account.index');
    Route::get('/find-by-id/{id}', [UserAccountController::class, 'findById'])->name('api.user-account.find-by-id');
    Route::get('/{id}', [UserAccountController::class, 'show'])->name('api.user-account.show');
    @Route::post('/', [UserAccountController::class, 'store'])->name('api.user-account.store');
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

// Transaction API Routes
Route::prefix('transactions')->group(function () {
    Route::get('/', [\App\Http\Controllers\TransactionController::class, 'index'])->name('api.transactions.index');
    Route::get('/by-user-account', [\App\Http\Controllers\TransactionController::class, 'byUserAccount'])->name('api.transactions.by-user-account');
    Route::get('/filter/period', [\App\Http\Controllers\TransactionController::class, 'filterByPeriod'])->name('api.transactions.filter-period');
    Route::delete('/group/{groupId}/hard', [\App\Http\Controllers\TransactionController::class, 'hardDeleteByGroupId']);
    Route::post('/Transaction', [TransactionController::class, 'Insert'])->name('api.transactions.insert');
    Route::get('/spending/summary', [TransactionController::class, 'spendingSummaryByPeriod'])->name('api.transactions.spending-summary');
});

// Financial Account API Routes
Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts'])->name('api.financial-account.active');
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');

    // Liquid Assets Route - per user_account_id
    Route::get('/liquid-assets/{user_account_id}', [FinancialAccountController::class, 'getUserLiquidAssets'])->name('api.financial-account.liquid-assets.user');
    // Liquid Assets Route - semua user
    Route::get('/liquid-assets/all-users', [FinancialAccountController::class, 'getAllUsersLiquidAssets'])->name('api.financial-account.liquid-assets.all-users');
    // Liquid Assets Summary untuk admin
    Route::get('/liquid-assets/admin/summary', [FinancialAccountController::class, 'adminLiquidAssetsSummary'])->name('api.financial-account.liquid-assets.admin-summary');
});

// =============================================================
// 3. REPORTS
// =============================================================
Route::prefix('reports')->group(function () {
    Route::get('/transactions-per-user-account', [ReportController::class, 'getTotalTransactionsPerUserAccount'])
        ->name('api.reports.transactions-per-user-account');

    Route::get('/surplus-deficit', [ReportController::class, 'surplusDeficitByPeriod'])
        ->name('api.reports.surplus-deficit');
    Route::get('/sum-by-type', [ReportController::class, 'sumFinancialAccountsByType'])
        ->name('api.reports.sum-by-type');
    Route::get(
        '/surplus-defisit', [ReportController::class, 'surplusDefisitByPeriod'])
        ->name('api.reports.surplus-defisit');
});

// =============================================================
// 4. TRANSACTION (Tambahan dari Incoming Change)
// =============================================================
Route::get('/getLatestActivities', [\App\Http\Controllers\TransactionController::class, 'getLatestActivities']);

Route::get(
    '/admin/reports/spending-summary',
    [\App\Http\Controllers\ReportController::class, 'adminSpendingSummary']
);

Route::get('/users/{id}/accounts', [UserController::class, 'getUserAccounts'])->name('api.users.accounts');

Route::get(
    '/admin/reports/cashin-by-period',
    [\App\Http\Controllers\ReportController::class, 'adminCashinByPeriod']
);
<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAccountTestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\TransactionController;
use App\Models\FinancialAccount;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request as HttpRequest;
// Explicit FQCN below for TransactionController to avoid analyzer confusion
use App\Http\Controllers\FinancialAccountController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'ProsesLogin']);

Route::get('/userlog', [UserController::class, 'AmbilDataUserYangLogin']);


// =============================================================
// 1. USER ACCOUNT (Prioritas versi Kamu: ada storeRaw & destroyRaw)
// =============================================================
// User API Routes
Route::post('/users', [UserController::class, 'createUserRaw']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
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


Route::prefix('user-accounts')->group(function () {
    Route::get('/total-gaji-semua-user', [UserAccountController::class, 'getTotalGajiSemuaUser']);
    Route::get('/hitung-total/{userId}', [UserAccountController::class, 'countAccountsPerUser']);
    Route::get('/', [UserAccountController::class, 'index']);
    Route::get('/{id}', [UserAccountController::class, 'show']);
});

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

Route::get('account-user/nested-structure', [UserAccountController::class, 'GetstructureNested'])->name('api.user-account.nested-structure');

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::get('/accounts', function () {
    return response()->json(['ok' => true]);
});
Route::get('/admin/active-user-accounts', [\App\Http\Controllers\UserAccountController::class, 'listActive']);

Route::post('/financial_accounts', [AccountController::class, 'store']);
Route::get('/financial_accounts', [AccountController::class, 'index']);
Route::get('/financial_accounts/{id}', [AccountController::class, 'show']);

// Transaction API Routes
Route::prefix('transactions')->group(function () {
    Route::get('/', [\App\Http\Controllers\TransactionController::class, 'index'])->name('api.transactions.index');
    Route::get('/by-user-account', [\App\Http\Controllers\TransactionController::class, 'byUserAccount'])->name('api.transactions.by-user-account');
    Route::get('/filter/period', [\App\Http\Controllers\TransactionController::class, 'filterByPeriod'])->name('api.transactions.filter-period');
    Route::get('/search', [\App\Http\Controllers\TransactionController::class, 'search'])->name('api.transactions.search');
    Route::delete('/group/{groupId}/hard', [\App\Http\Controllers\TransactionController::class, 'hardDeleteByGroupId']);
    Route::post('/Transaction', [TransactionController::class, 'Insert'])->name('api.transactions.insert');
    Route::put('/{id}', [TransactionController::class, 'update'])->name('api.transactions.update');
    Route::get('/spending/summary', [TransactionController::class, 'spendingSummaryByPeriod'])->name('api.transactions.spending-summary');
    Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('api.transactions.deleteByGroupIdRaw');
});

// Financial Account API Routes
Route::prefix('financial-account')->group(function () {
    Route::get('/active', [FinancialAccountController::class, 'getActiveAccounts'])->name('api.financial-account.active');
    Route::get('/{id}', [FinancialAccountController::class, 'show'])->name('api.financial-account.show');
    Route::get('/filter/type/{type}', [FinancialAccountController::class, 'filterByType'])->name('api.financial-account.filter-by-type');

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
    Route::get('/sum-cashin-by-period', [ReportController::class, 'sumCashInByPeriod'])
        ->name('api.reports.sum-cashin-by-period');
    Route::get('/group-balance-by-account-type', [ReportController::class, 'getGroupBalanceByAccountType'])
        ->name('api.reports.group-balance-by-account-type');
});

// =============================================================
// FINANCIAL ACCOUNT - SOFT DELETE / SET INACTIVE
// =============================================================
Route::prefix('financial-accounts')->controller(FinancialAccountController::class)->group(function () {
    // Get all active accounts
    Route::get('/', 'getActiveAccounts');
    
    // Get account detail by ID
    Route::get('/{id}', 'show')->whereNumber('id');
    
    // Soft delete single account
    Route::delete('/{id}/soft-delete', 'softDelete')->whereNumber('id');
    
    // Restore soft-deleted account
    Route::post('/{id}/restore', 'restore')->whereNumber('id');
    
    // Get all inactive accounts (trash/recycle bin)
    Route::get('/trash/all', 'getInactiveAccounts');
    
    // Soft delete multiple accounts
    Route::post('/batch/soft-delete', 'softDeleteMultiple');
    
    // Get statistics
    Route::get('/stats/summary', 'getStatistics');
});

// =============================================================
// 4. TRANSACTION (Tambahan dari Incoming Change)
// =============================================================
Route::get('/getLatestActivities', [\App\Http\Controllers\TransactionController::class, 'getLatestActivities']);

Route::get(
    '/admin/reports/spending-summary',
    [\App\Http\Controllers\ReportController::class, 'adminSpendingSummary']
);

Route::get(
    '/admin/reports/expenses-summary',
    [\App\Http\Controllers\ReportController::class, 'adminExpensesSummary']
);

Route::get('/users/{id}/accounts', [UserController::class, 'getUserAccounts'])->name('api.users.accounts');
Route::get('/users', [UserController::class, 'getUsers'])->name('api.users.get-users');
Route::get('/users-without-account', [UserController::class, 'getUsersWithoutAccount'])->name('api.users.without-account');

Route::get(
    '/admin/reports/cashin-by-period',
    [\App\Http\Controllers\ReportController::class, 'adminCashinByPeriod']
);

Route::get(
    '/admin/income/by-period',
    [\App\Http\Controllers\ReportController::class, 'adminIncomeByPeriod']
);

Route::post('/test-login', [UserAccountTestController::class, 'testLogin']);


Route::get('/users/admin/search', [UserController::class, 'searchUsers']);

Route::get('/users/count-by-date', [UserController::class, 'countUserpertanggalandbulan']);

Route::post('/account/update-password/{id}', [AccountController::class, 'updatePassword']);
Route::get('/search', [\App\Http\Controllers\TransactionController::class, 'search'])->name('api.transactions.search');


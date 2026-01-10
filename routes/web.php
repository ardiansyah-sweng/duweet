<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// ==========================
// Test Liquid Assets
// ==========================
Route::get('/test/liquid-assets', function () {
    try {
        $rows = DB::table('users as u')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.user_id', '=', 'u.id')
            ->leftJoin('financial_accounts as fa', function ($join) {
                $join->on('fa.id', '=', 'ufa.financial_account_id')
                     ->where('fa.type', 'AS');
            })
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                DB::raw('COALESCE(SUM(ufa.balance),0) as total_asset')
            )
            ->groupBy('u.id','u.name')
            ->orderByDesc('total_asset')
            ->get();

        return response()->json($rows);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// ==========================
// Accounts Routes
// ==========================
Route::post('/accounts', [AccountController::class, 'store']);
Route::get('/accounts', [AccountController::class, 'index']);
Route::get('/accounts/{id}', [AccountController::class, 'show']);

// ==========================
// Financial Accounts
// ==========================
Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

// ==========================
// Reports (public)
// ==========================
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// ==========================
// ADMIN ROUTES (with auth middleware)
// ==========================
Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/cashout/sum', [AdminReportController::class, 'showCashoutSumForm'])
        ->name('admin.cashout.sum.form');

    Route::post('/cashout/sum', [AdminReportController::class, 'getCashoutSumByPeriod'])
        ->name('admin.cashout.sum.result');

    Route::post('/cashout/sum/export', [AdminReportController::class, 'exportCashoutCsv'])
        ->name('admin.cashout.sum.export');
});

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| WEB HOME
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| DEV / TEST ROUTES (NON-PRODUCTION)
|--------------------------------------------------------------------------
| ⚠️ Sebaiknya dibungkus middleware env=local
*/
Route::middleware('env:local')->group(function () {
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
                ->groupBy('u.id', 'u.name')
                ->orderByDesc('total_asset')
                ->get();

            return response()->json($rows);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
});

/*
|--------------------------------------------------------------------------
| WEB ACCOUNTS (OPTIONAL – ADMIN / VIEW ONLY)
|--------------------------------------------------------------------------
*/
Route::prefix('web/accounts')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::get('/{id}', [AccountController::class, 'show'])->whereNumber('id');
});

/*
|--------------------------------------------------------------------------
| FINANCIAL ACCOUNTS (WEB VIEW)
|--------------------------------------------------------------------------
*/
Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

/*
|--------------------------------------------------------------------------
| REPORTS (WEB VIEW)
|--------------------------------------------------------------------------
*/
Route::get('/reports/income-summary', [ReportController::class, 'incomeSummary']);

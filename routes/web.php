<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CountUserPerTanggalBulan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Expense
|--------------------------------------------------------------------------
*/
Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);

/*
|--------------------------------------------------------------------------
| Account
|--------------------------------------------------------------------------
*/
Route::post('/accounts', [AccountController::class, 'store']);
Route::get('/accounts', [AccountController::class, 'index']);
Route::get('/accounts/{id}', [AccountController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Financial Account
|--------------------------------------------------------------------------
*/
Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

/*
|--------------------------------------------------------------------------
| Report
|--------------------------------------------------------------------------
*/
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

/*
|--------------------------------------------------------------------------
| Test / Debug Route
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| COUNT USER PER TANGGAL & BULAN
|--------------------------------------------------------------------------
*/
Route::get('/user/per-tanggal', [CountUserPerTanggalBulan::class, 'countUserPerTanggal']);
Route::get('/user/per-bulan', [CountUserPerTanggalBulan::class, 'countUserPerBulan']);

<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/test/liquid-assets', function () {
    try {
        $rows = DB::table('users as u')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.user_id', '=', 'u.id')
            ->leftJoin('financial_accounts as fa', function ($join) {
                $join->on('fa.id', '=', 'ufa.financial_account_id')
                     ->where('fa.type', 'AS');
            })
            ->select('u.id as user_id','u.name as user_name', DB::raw('COALESCE(SUM(ufa.balance),0) as total_asset'))
            ->groupBy('u.id','u.name')
            ->orderByDesc('total_asset')
            ->get();

        return response()->json($rows);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::post('/accounts', [AccountController::class, 'store']);
Route::get('/accounts', [AccountController::class, 'index']);
Route::get('/accounts/{id}', [AccountController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Financial Accounts
Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

// Reports
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

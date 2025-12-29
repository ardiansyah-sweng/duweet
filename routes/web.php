<?php

<<<<<<< HEAD
=======
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\DB;
>>>>>>> 48360fa7025e7384ef84dd10d7f8e913b6aee162
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\FinancialAccountController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);
Route::patch('/transactions/{id}', [TransaksiController::class, 'update']);
Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);
<<<<<<< HEAD
=======

use App\Http\Controllers\FinancialAccountController; 
use App\Http\Controllers\ReportController; // PENTING: Import Controller
use App\Http\Controllers\TransaksiController;

>>>>>>> 48360fa7025e7384ef84dd10d7f8e913b6aee162
Route::get('/test/liquid-assets', function () {
    try {
        $rows = DB::table('users as u')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.user_id', '=', 'u.id')
            ->leftJoin('financial_accounts as fa', function ($join) {
                $join->on('fa.id', '=', 'ufa.financial_account_id')
                     ->where('fa.type', 'AS');
            })
            ->select('u.id as user_id','u.name as user_name', DB::raw('COALESCE(SUM(ufa.balance),0) as total_asset'))
            use Illuminate\Support\Facades\Route;
            use Illuminate\Support\Facades\DB;
            use App\Http\Controllers\ReportController;
            use App\Http\Controllers\TransaksiController;
            use App\Http\Controllers\AccountController;
            use App\Http\Controllers\MonthlyExpenseController;
            use App\Http\Controllers\FinancialAccountController;

            Route::get('/', function () {
                return view('welcome');
            });

            Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);
            Route::patch('/transactions/{id}', [TransaksiController::class, 'update']);
            Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);
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
            Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

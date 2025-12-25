<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialAccountController; 
use App\Http\Controllers\ReportController; // PENTING: Import Controller

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
*/

Route::get('/', function () {
    return view('welcome');
});

// Web-only routes (no API routes here). API routes live in routes/api.php.

// Route report (punya dosen / sebelumnya)
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Tambahkan route lain di sini jika ada...
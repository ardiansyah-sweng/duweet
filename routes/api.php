<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Asset Report API Routes
|--------------------------------------------------------------------------
|
| Routes for financial asset reporting and analysis
|
*/

Route::prefix('assets')->name('assets.')->group(function () {
    
    // Summary and Dashboard Routes
    Route::get('/summary', [AssetReportController::class, 'getAssetSummary'])
        ->name('summary');
    
    Route::get('/dashboard', [AssetReportController::class, 'getAssetDashboard'])
        ->name('dashboard');
    
    // Detailed Reports
    Route::get('/report', [AssetReportController::class, 'getAssetReport'])
        ->name('report');
    
    Route::get('/performance', [AssetReportController::class, 'getAssetPerformance'])
        ->name('performance');
    
    Route::get('/allocation', [AssetReportController::class, 'getAssetAllocation'])
        ->name('allocation');
    
    Route::get('/liquidity', [AssetReportController::class, 'getLiquidityBreakdown'])
        ->name('liquidity');
    
    Route::get('/liquidity/details', [AssetReportController::class, 'getLiquidityDetails'])
        ->name('liquidity.details');
    
    Route::get('/hierarchy', [AssetReportController::class, 'getAssetHierarchy'])
        ->name('hierarchy');
    
    // Top Performers
    Route::get('/top-performers', [AssetReportController::class, 'getTopPerformingAssets'])
        ->name('top-performers');
    
    // Trends (Future feature)
    Route::get('/trends', [AssetReportController::class, 'getAssetTrends'])
        ->name('trends');
    
    // Export
    Route::get('/export', [AssetReportController::class, 'exportAssetData'])
        ->name('export');
    
    // Individual Account Details
    Route::get('/account/{accountId}', [AssetReportController::class, 'getAccountDetails'])
        ->name('account.details')
        ->where('accountId', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| Additional API Routes (Future Extensions)
|--------------------------------------------------------------------------
|
| Placeholder for additional financial API endpoints
|
*/

// Route::prefix('financial')->name('financial.')->group(function () {
//     // Transactions
//     Route::apiResource('transactions', TransactionController::class);
//     
//     // Budgets
//     Route::apiResource('budgets', BudgetController::class);
//     
//     // Categories
//     Route::apiResource('categories', CategoryController::class);
// });
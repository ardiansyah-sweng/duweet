<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialAccountController;

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

// Route dasar Laravel
Route::get('/', function () {
    return view('welcome');
});

// Route GET endpoint incomeSummary
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Route GET endpoint untuk Financial Accounts yang aktif
Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

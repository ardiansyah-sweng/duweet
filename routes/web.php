<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;


Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);

use App\Http\Controllers\FinancialAccountController; 
use App\Http\Controllers\ReportController; // PENTING: Import Controller


Route::get('/', function () {
    return view('welcome');
});

// Web-only routes (no API routes here). API routes live in routes/api.php.

// Route GET yang Benar untuk endpoint incomeSummary
// FIX: Menggantikan syntax lama dengan syntax array [Controller::class, 'method']
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

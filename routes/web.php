<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialAccountController;

// -------------------------------------------------------------
// Web Routes
// -------------------------------------------------------------

// Route dasar bawaan Laravel
Route::get('/', function () {
    return view('welcome');
});

// Route GET endpoint incomeSummary
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);

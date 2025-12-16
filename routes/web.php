<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Route report (punya dosen / sebelumnya)
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// ===============================
// TUGAS:
// Query ambil semua account milik user
// ===============================
Route::get(
    '/user-account/{id}/accounts',
    [UserAccountController::class, 'getAllAccounts']
)->name('api.user-account.accounts');

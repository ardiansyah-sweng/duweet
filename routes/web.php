<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController; // PENTING: Import Controller

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

// Route Dasar Laravel
Route::get('/', function () {
    return view('welcome');
});

// Route GET yang Benar untuk endpoint incomeSummary
// FIX: Menggantikan syntax lama dengan syntax array [Controller::class, 'method']
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Route untuk admin: ringkasan pengeluaran per periode
Route::get('/report/expenses-summary-admin', [ReportController::class, 'expensesSummaryAdmin']);

// Tambahkan route lain di sini jika ada...
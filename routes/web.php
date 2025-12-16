<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController; // PENTING: Import Controller
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

// Route Dasar Laravel
Route::get('/', function () {
    return view('welcome');
});

// Route GET yang Benar untuk endpoint incomeSummary
// FIX: Menggantikan syntax lama dengan syntax array [Controller::class, 'method']
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Tambahkan route lain di sini jika ada...
// Convenience web routes so hitting `/financial-account` in tools like Postman
// (or browsers) will return the same JSON as the API endpoint.
// Use the 'api' middleware so these helper web routes don't trigger session middleware
// (avoids requiring the DB sessions table during lightweight API testing tools).
Route::get('/financial-account', [FinancialAccountController::class, 'index'])->middleware('api');
Route::get('/financial-account/{id}', [FinancialAccountController::class, 'show'])->middleware('api');
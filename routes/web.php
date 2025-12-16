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
// Helper web routes so requests to `/financial-account` work in tools like Postman.
// These routes explicitly remove session/cookie middleware to avoid depending on the
// database-backed sessions table in development. They still use the controller
// implementation so behaviour stays consistent with the API.
Route::get('/financial-account', [FinancialAccountController::class, 'index'])
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ]);

Route::get('/financial-account/{id}', [FinancialAccountController::class, 'show'])
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ]);
<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController; // PENTING: Import Controller

<<<<<<< HEAD
Route::get('/total-per-user', [AccountController::class, 'totalPerUser']);
// Load API routes (if present) so we can keep API routes in routes/api.php
if (file_exists(__DIR__ . '/api.php')) {
    require __DIR__ . '/api.php';
}
=======
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
>>>>>>> 704974a8edd2f12696008b0f7dd219ec55e5e922
Route::get('/', function () {
    return view('welcome');
});

// Route GET yang Benar untuk endpoint incomeSummary
// FIX: Menggantikan syntax lama dengan syntax array [Controller::class, 'method']
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Tambahkan route lain di sini jika ada...
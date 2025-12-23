<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController; // PENTING: Import Controller

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

// Tambahkan route lain di sini jika ada...
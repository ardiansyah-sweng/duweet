<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\UserNestedController;
=======
use App\Http\Controllers\ReportController; // PENTING: Import Controller
>>>>>>> b38c826a09b03375aec23465b6c822809a669a85

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
    return redirect('/nested');
});

<<<<<<< HEAD
Route::get('/nested', [UserNestedController::class, 'index']);
=======
// Route GET yang Benar untuk endpoint incomeSummary
// FIX: Menggantikan syntax lama dengan syntax array [Controller::class, 'method']
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Tambahkan route lain di sini jika ada...
>>>>>>> b38c826a09b03375aec23465b6c822809a669a85

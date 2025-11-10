<?php
// Daftarkan controller-mu di sini
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;
// Ini adalah URL yang akan kamu buka di browser
Route::get('/laporan-pengeluaran', [AdminDashboardController::class, 'showExpenseReport']);

Route::get('/', function () {
    return view('welcome');
});

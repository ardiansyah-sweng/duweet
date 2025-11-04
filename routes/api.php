<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route API untuk laporan dan pengecekan sistem
| akan terdaftar di sini. Pastikan sesuai dengan method
| di dalam ReportController.
|
*/

// 🔹 Tes koneksi API
Route::get('/ping', fn () => response()->json(['pong' => true]));

// 🔹 Endpoint dummy untuk pengecekan
Route::get('/accounts', fn () => response()->json(['ok' => true]));

// 🔹 Report endpoints (kelompokkan dalam prefix 'report' agar rapi)



    Route::get('without-accounts', [ReportController::class, 'usersWithoutAccounts']);

    // Menampilkan semua user tanpa akun aktif
    Route::get('without-active-accounts', [ReportController::class, 'usersWithoutActiveAccounts']);

    // Menampilkan seluruh aset likuid berdasarkan user_id
    Route::get('{id}/liquid-assets', [ReportController::class, 'userLiquidAsset']);


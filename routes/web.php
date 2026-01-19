<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Laravel OK';
});


// API endpoint: grouped user balances by account type
Route::get('/api/users/{user}/balances-by-type', [UserBalanceController::class, 'index']);

// Backwards-compatible web routes that mirror API routes.
// Some environments may not load api.php with the expected prefix; adding these ensures
// calls to /api/... paths still reach the controller.
Route::get('/api/ping', function () {
    return response()->json(['ok' => true]);
});

Route::get('/api/users', [UserBalanceController::class, 'byQuery']);
Route::get('/api/users/balances', [UserBalanceController::class, 'byQuery']);
Route::get('/api/users/{user}/balances', [UserBalanceController::class, 'index']);


// Route GET yang Benar untuk endpoint incomeSummary
// FIX: Menggantikan syntax lama dengan syntax array [Controller::class, 'method']
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

// Tambahkan route lain di sini jika ada...
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserBalanceController;

Route::get('/', function () {
    return view('welcome');
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


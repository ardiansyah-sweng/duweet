<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserBalanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/users/{user}/balances', [UserBalanceController::class, 'index']);

// Alternate endpoint: resolve user from query params (user_id, user_account_id or email)
// Example: GET /api/users/balances?user_account_id=12
Route::get('/users/balances', [UserBalanceController::class, 'byQuery']);

// Accept requests to /api/users?user_account_id=... for backward-compatibility with Postman calls
Route::get('/users', [UserBalanceController::class, 'byQuery']);

// Simple health check to verify routes are loaded
Route::get('/ping', function () {
	return response()->json(['ok' => true]);
});

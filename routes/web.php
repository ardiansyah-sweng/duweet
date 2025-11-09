<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Endpoint to reset user account password by email (for testing via Postman)
Route::post('/user/reset-password', [\App\Http\Controllers\UserAccountController::class, 'resetPassword']);

// Also expose an API-style path that uses the 'api' middleware group so Postman
// can call /api/user/reset-password without CSRF tokens.
// The API endpoints are served from routes/api.php. No explicit /api route in web.php needed.
// For environments where routes/api.php isn't auto-prefixed, expose an explicit
// API-style path for finding user by email (GET) so Postman can use /api/user/find.
Route::get('/api/user/find', [\App\Http\Controllers\UserAccountController::class, 'findByEmail'])->middleware('api');

// Explicit POST API route so clients can call /api/user/reset-password without CSRF
Route::post('/api/user/reset-password', [\App\Http\Controllers\UserAccountController::class, 'resetPassword'])->middleware('api');
Route::get('/api/user/reset-password', [\App\Http\Controllers\UserAccountController::class, 'resetPasswordViaGet'])->middleware('api');

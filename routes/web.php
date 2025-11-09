<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// If RouteServiceProvider is not present/auto-loading api routes in this project,
// include routes/api.php here so API endpoints are registered when running locally.
if (file_exists(__DIR__ . '/api.php')) {
    require __DIR__ . '/api.php';
}

// Fallback: register API route on web routes if API routes are not loaded by RouteServiceProvider.
use App\Http\Controllers\Api\FinancialAccountController;
Route::get('/api/financial-accounts', [FinancialAccountController::class, 'index']);

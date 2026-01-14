<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialAccountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are intended for stateless API access (use Postman or any
| HTTP client). The `financial-accounts` endpoint accepts an optional
| `type` query parameter (e.g. ?type=AS or ?type=IN,EX).
| Query parameter `summary=1` will include summary data.
|
*/

// Public endpoints - returns all accounts (filterable by type)
Route::get('/financial-accounts', [FinancialAccountController::class, 'index']);
Route::get('/financial-accounts/types', [FinancialAccountController::class, 'types']);
Route::get('/financial-accounts/summary', [FinancialAccountController::class, 'summary']);

// Authenticated endpoint - returns user's linked accounts
Route::middleware('auth:sanctum')->get('/user/financial-accounts', [FinancialAccountController::class, 'index']);
Route::middleware('auth:sanctum')->get('/user/financial-accounts/summary', [FinancialAccountController::class, 'summary']);

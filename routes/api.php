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
|
*/

// Public endpoint - returns all accounts (filterable by type)
Route::get('/financial-accounts', [FinancialAccountController::class, 'index']);

// Authenticated endpoint example (if you use sanctum/passport)
// Remove or enable depending on your project's auth setup.
Route::middleware('auth:sanctum')->get('/user/financial-accounts', [FinancialAccountController::class, 'index']);

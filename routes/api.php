<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;
use App\Http\Controllers\UserAccountController;

// Monthly expenses
Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);

// UserAccount API Routes (no CSRF protection needed)
// tambahkan route UserAccount sesuai kebutuhan, contoh:
Route::get('/user-accounts', [UserAccountController::class, 'index']);
Route::get('/user-accounts/{id}', [UserAccountController::class, 'show']);
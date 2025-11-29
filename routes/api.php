<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MonthlyExpenseController;


Route::get('/expenses/monthly', [MonthlyExpenseController::class, 'monthly']);




use App\Http\Controllers\UserAccountController;

// UserAccount API Routes (no CSRF protection needed)
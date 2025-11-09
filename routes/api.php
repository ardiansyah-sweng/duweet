<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/reset-password', [\App\Http\Controllers\UserAccountController::class, 'resetPassword']);

// GET endpoint to find user by email (safe response, no password)
Route::get('/user/find', [\App\Http\Controllers\UserAccountController::class, 'findByEmail']);

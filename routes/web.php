<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountBalanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/balance/total/{userId}', [AccountBalanceController::class, 'totalBalanceUser']);

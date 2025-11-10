<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountBalanceController;


    Route::get('/balance/total/{userId}', [AccountBalanceController::class, 'totalBalanceUser']);
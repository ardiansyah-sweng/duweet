<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\UserAccountController;
Route::get('/user-accounts/tidak-login', [UserAccountController::class, 'tidakLogin']);
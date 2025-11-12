<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk menampilkan semua akun user
Route::get('/user/{id}/accounts', [UserController::class, 'showAccounts']);

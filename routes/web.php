<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController; 
Route::get('/', function () {
    return view('welcome');
});

Route::post('/process-login', [LoginController::class, 'authenticate'])->name('login.attempt'); 

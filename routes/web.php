<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; // Tambahkan ini

// Route bawaan Laravel (halaman utama)
Route::get('/', function () {
    return view('welcome');
});

// Route untuk update data user
Route::middleware('auth')->put('/user/update', [UserController::class, 'update'])->name('user.update');

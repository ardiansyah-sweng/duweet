<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

// ROUTE ADMIN (tanpa auth dulu)
Route::prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users.index');
});

<?php 

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

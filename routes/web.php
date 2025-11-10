<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/users', [Users::class, 'index']);

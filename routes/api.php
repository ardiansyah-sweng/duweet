<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users/create-raw', [UserController::class, 'createUserRaw']);
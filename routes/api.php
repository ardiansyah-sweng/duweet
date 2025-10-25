<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::delete('/users/{id}', [UserController::class, 'destroy']);

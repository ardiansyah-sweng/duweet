<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserSearchController;

Route::get('/users/search', [UserSearchController::class, 'search']);
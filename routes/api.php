<?php

use App\Http\Controllers\AccountController;

Route::get('/accounts/nested', [AccountController::class, 'index']);
Route::get('/accounts/nested/{id}', [AccountController::class, 'show']);

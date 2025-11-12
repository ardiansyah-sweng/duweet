<?php
use App\Http\Controllers\UserController;

Route::get('/user/{id}/accounts', [UserController::class, 'getAllAccounts']);

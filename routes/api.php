<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('{id}/accounts', [UserController::class, 'getAllAccounts']);
});

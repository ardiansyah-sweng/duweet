<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserNestedController;

Route::get('/', function () {
    return redirect('/nested');
});

Route::get('/nested', [UserNestedController::class, 'index']);

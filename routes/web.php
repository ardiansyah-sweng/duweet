<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\TransaksiController;

Route::patch('/transactions/{id}', [TransaksiController::class, 'update']);

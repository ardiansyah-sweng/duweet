<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::get('/', function () {
    return view('welcome');
});

// Route Statistik User
Route::get('/user/count/tanggal', [Controller::class, 'countUserPerTanggal']);
Route::get('/user/count/bulan', [Controller::class, 'countUserPerBulan']);

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

// Tambahan route baru untuk lihat semua user
Route::get('/users', function () {
    $users = DB::table('users')->get();
    return $users;
});

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserNestedController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/nested', [UserNestedController::class, 'index']);

// Tambahan route baru untuk lihat semua user
Route::get('/users', function () {
    $users = DB::table('users')->get();
    return $users;
});

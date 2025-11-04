<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\User;

Route::get('/count-user', function () {
    $total = User::count();
    return response()->json(['total_users' => $total]);
});

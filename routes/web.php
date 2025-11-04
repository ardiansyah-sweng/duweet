<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< HEAD

use App\Models\User;

Route::get('/count-user', function () {
    $total = User::count();
    return response()->json(['total_users' => $total]);
});
=======
>>>>>>> 6f3325679e06485059f2a5f2fe38054cb01314bf

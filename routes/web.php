<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Web-only routes (no API routes here). API routes live in routes/api.php.

<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/total-per-user', [AccountController::class, 'totalPerUser']);
// Load API routes (if present) so we can keep API routes in routes/api.php
if (file_exists(__DIR__ . '/api.php')) {
    require __DIR__ . '/api.php';
}
Route::get('/', function () {
    return view('welcome');
});

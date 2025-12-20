<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserNestedController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route dasar
Route::get('/', function () {
    return redirect('/nested');
});

// Nested account view
Route::get('/nested', [UserNestedController::class, 'index']);

// Report income summary
Route::get('/report/income-summary', [ReportController::class, 'incomeSummary']);

<?php

use Illuminate\Support\Facades\Route;

// Existing route
Route::get('/', function () {
    return view('welcome');
});

// Add the admin report route here
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('reports/surplus', [\App\Http\Controllers\Admin\ReportController::class, 'surplusByPeriod'])
        ->name('reports.surplus');
});

// Temporary debug route (no auth) for testing with tools like Thunder Client.
// Remove or protect this route before deploying to production.
Route::get('admin/reports/surplus-debug', [\App\Http\Controllers\Admin\ReportController::class, 'surplusByPeriod'])
    ->name('reports.surplus.debug');

// quick ping route to verify route loading
Route::get('ping', function () {
    return response('pong');
});

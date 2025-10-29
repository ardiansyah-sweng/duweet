<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReportController;

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::get('/accounts', function () {
    return response()->json(['ok' => true]);
});


Route::post('/accounts', [AccountController::class, 'store']);
Route::get('/accounts', [AccountController::class, 'index']);
Route::get('/accounts/{id}', [AccountController::class, 'show']);

Route::get('/reports/liquid-assets', [ReportController::class, 'liquidAssets']);






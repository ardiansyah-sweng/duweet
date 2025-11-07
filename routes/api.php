<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialAccountController;

Route::get('/ping', fn () => response()->json(['pong' => true]));


Route::get('/report/admin/liquid-assets-per-user', [ReportController::class, 'adminLiquidAssetsPerUser']);

Route::get('/financial-account/{id}', [FinancialAccountController::class, 'show']);

Route::put('/financial-account/{id}/balance', [FinancialAccountController::class, 'updateBalance']);
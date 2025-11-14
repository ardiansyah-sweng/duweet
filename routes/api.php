<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;


    Route::get('/ping', fn () => response()->json(['pong' => true]));
    Route::get('/accounts', fn () => response()->json(['ok' => true]));
    Route::get('without-accounts', [ReportController::class, 'usersWithoutAccounts']);
    Route::get('without-active-accounts', [ReportController::class, 'usersWithoutActiveAccounts']);
    Route::get('{id}/liquid-assets', [ReportController::class, 'userLiquidAsset']);


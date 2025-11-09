<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialAccountController;

Route::get('/', function () {
    return view('welcome');
});

// Financial accounts list / filter by type
Route::get('/financial-accounts', [FinancialAccountController::class, 'index']);

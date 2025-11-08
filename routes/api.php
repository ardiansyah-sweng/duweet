<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;

Route::get('/reports/transactions-per-user', [ReportsController::class, 'getTotalTransactionsPerUser']);


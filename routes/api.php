<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

// Route untuk laporan total spending (by period)
Route::get('/spending-summary', [TransactionController::class, 'spendingSummary']);

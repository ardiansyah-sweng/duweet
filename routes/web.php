<?php

// routes/web.php


use App\Http\Controllers\ReportController;

Route::get('/report/income-summary', [ReportController::class, 'getIncomeSumByPeriode']);

// ----> TAMBAHKAN BARIS DI BAWAH INI <----

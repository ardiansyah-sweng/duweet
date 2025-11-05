<?php

use App\Http\Controllers\FinancialAccountController; 

Route::get('/financial-accounts/active', [FinancialAccountController::class, 'getActiveAccounts']);
<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use Illuminate\Http\Request;

class FinancialAccountController extends Controller
{
    public function getActiveAccounts(Request $request)
    {
        $activeAccounts = FinancialAccount::active()->get();

        return response()->json([
            'message' => 'Daftar Akun Keuangan yang Aktif',
            'count' => $activeAccounts->count(),
            'data' => $activeAccounts
        ]);
    }
}

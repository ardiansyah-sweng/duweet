<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    // ... existing code ...

    public function showParentBalance($parentId)
    {
        // Memanggil fungsi Raw Query dari Model
        $data = FinancialAccount::getParentBalanceRaw($parentId);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parent account tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
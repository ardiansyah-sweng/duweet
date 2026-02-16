<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // ... existing code ...

    public function showRunningBalance($accountId)
    {
        // Memanggil fungsi Raw Query dari Model
        $transactions = Transaction::getTransactionsWithRunningBalance($accountId);

        if (empty($transactions)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada transaksi atau akun tidak ditemukan',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'account_id' => $accountId,
            'data' => $transactions
        ]);
    }
}
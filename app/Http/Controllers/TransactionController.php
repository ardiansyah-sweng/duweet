<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Detail transaksi berdasarkan ID
     */
    public function show($id)
    {
        $result = Transaction::getDetailById($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}

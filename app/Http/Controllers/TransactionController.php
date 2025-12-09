<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        try {
            $activities = Transaction::getLatestActivitiesRaw();
            return response()->json([
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        try {
            // Panggil fungsi static yang sudah kita perbaiki di model Transaction
            $activities = Transaction::getLatestActivitiesRaw();
            
            // Kembalikan data sebagai JSON dengan format yang Anda inginkan
            return response()->json([
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan pesan error
            return response()->json([
                'message' => 'Gagal mengambil data transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

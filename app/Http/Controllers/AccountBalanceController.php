<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountBalanceController extends Controller
{
    /**
     * Menghitung total saldo semua akun milik user berdasarkan user_id
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalBalanceUser($userId)
    {
        // Ganti 'accounts' dan 'balance' sesuai nama tabel dan kolom di database kamu
        $total = DB::table('accounts')
            ->where('user_id', $userId)
            ->sum('balance');

        return response()->json([
            'user_id' => $userId,
            'total_balance' => $total,
        ]);
    }
}

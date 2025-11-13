<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Ambil detail transaksi spesifik berdasarkan ID
     * Menggunakan query DML (JOIN antar tabel)
     */
    public function show($id)
    {
        $result = DB::table('transactions as t')
            ->join('user_accounts as ua', 'ua.id', '=', 't.user_account_id')
            ->join('users as u', 'u.id', '=', 'ua.id_user')
            ->join('financial_accounts as fa', 'fa.id', '=', 't.financial_account_id')
            ->select(
                't.id as transaction_id',
                't.amount',
                't.entry_type',
                't.description',
                't.created_at as transaction_date',
                'ua.id as user_account_id',
                'ua.username as user_account_username',
                'ua.email as user_account_email',
                'u.id as id_user',
                'u.name as user_name',
                'fa.id as financial_account_id',
                'fa.name as financial_account_name'
            )
            ->where('t.id', $id)
            ->first();

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

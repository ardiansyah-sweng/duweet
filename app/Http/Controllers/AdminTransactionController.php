<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class AdminTransactionController extends Controller
{
    public function spendingSummaryAllPeriods()
    {
        // --- GABUNGKAN TRANSAKSI DENGAN AKUN USER ---
        $baseQuery = Transaction::join('user_financial_accounts', 'transactions.user_financial_account_id', '=', 'user_financial_accounts.id')
            ->join('user_accounts', 'user_financial_accounts.user_account_id', '=', 'user_accounts.id')
            ->where('transactions.type', 'debit');

        // ============= HARIAN =============
        $daily = (clone $baseQuery)
            ->select(
                'user_accounts.account_name',
                DB::raw('DATE(transactions.created_at) as period'),
                DB::raw('SUM(transactions.amount) as total_spending')
            )
            ->groupBy('user_accounts.account_name', DB::raw('DATE(transactions.created_at)'))
            ->orderBy('period', 'asc')
            ->get();

        // ============= MINGGUAN =============
        $weekly = (clone $baseQuery)
            ->select(
                'user_accounts.account_name',
                DB::raw('YEARWEEK(transactions.created_at, 1) as period'),
                DB::raw('SUM(transactions.amount) as total_spending')
            )
            ->groupBy('user_accounts.account_name', DB::raw('YEARWEEK(transactions.created_at, 1)'))
            ->orderBy('period', 'asc')
            ->get();

        // ============= BULANAN =============
        $monthly = (clone $baseQuery)
            ->select(
                'user_accounts.account_name',
                DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m") as period'),
                DB::raw('SUM(transactions.amount) as total_spending')
            )
            ->groupBy('user_accounts.account_name', DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'))
            ->orderBy('period', 'asc')
            ->get();

        // --- KEMBALIKAN SEMUA HASIL ---
        return response()->json([
            'status' => 'success',
            'summary' => [
                'daily' => $daily,
                'weekly' => $weekly,
                'monthly' => $monthly,
            ],
        ]);
    }
}

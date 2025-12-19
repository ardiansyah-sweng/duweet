<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\FinancialAccountColumns;
use App\Constants\UserAccountColumns;

class SpendingReportController extends Controller
{
    /**
     * Sum spending by admin (by period)
     */
    public function index(Request $request)
    {
        // ==============================
        // 1. VALIDATION
        // ==============================
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $from = $request->query('from');
        $to   = $request->query('to');

        // ==============================
        // 2. QUERY SUM SPENDING
        // ==============================
        $data = DB::table(config('db_tables.transaction') . ' as t')
    ->join(
        config('db_tables.financial_account') . ' as fa',
        'fa.id',
        '=',
        't.' . TransactionColumns::FINANCIAL_ACCOUNT_ID
    )
    ->join(
        config('db_tables.user_account') . ' as ua',
        'ua.id',
        '=',
        't.' . TransactionColumns::USER_ACCOUNT_ID
    )
    ->where('fa.' . FinancialAccountColumns::TYPE, 'EX', 'SP')
    ->where('t.' . TransactionColumns::ENTRY_TYPE, 'debit')
    ->whereBetween('t.' . TransactionColumns::CREATED_AT, [$from, $to])
    ->groupBy('ua.' . UserAccountColumns::USERNAME)
    ->select(
        'ua.' . UserAccountColumns::USERNAME . ' as username',
        DB::raw('SUM(t.' . TransactionColumns::AMOUNT . ') as total_spending')
    )
    ->orderByDesc('total_spending')
    ->get();


        // ==============================
        // 3. RESPONSE
        // ==============================
        return response()->json([
            'success' => true,
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'data' => $data,
        ]);
    }
}

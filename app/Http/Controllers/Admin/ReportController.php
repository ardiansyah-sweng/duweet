<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function surplusByPeriod(Request $request)
    {
        $data = $request->validate([
            'start'  => 'required|date',
            'end'    => 'required|date',
            'period' => 'nullable|in:daily,monthly,yearly',
        ]);

        $start = Carbon::parse($data['start'])->startOfDay();
        $end   = Carbon::parse($data['end'])->endOfDay();
        $period = $data['period'] ?? 'monthly';

        // Validasi: pastikan start tidak lebih besar dari end
        if ($start->gt($end)) {
            return response()->json([
                'message' => 'The start date must be before or equal to the end date.'
            ], 422);
        }

        $formats = [
            'daily'   => "DATE_FORMAT(t.created_at, '%Y-%m-%d')",
            'monthly' => "DATE_FORMAT(t.created_at, '%Y-%m')",
            'yearly'  => "DATE_FORMAT(t.created_at, '%Y')",
        ];

        $periodExpr = $formats[$period];

        // Join ke financial_accounts untuk cek tipe akun
        $rows = DB::table('transactions as t')
            ->join('financial_accounts as fa', 't.financial_account_id', '=', 'fa.id')
            ->selectRaw(<<<SQL
                $periodExpr as period,
                SUM(CASE WHEN fa.type = 'IN' AND t.entry_type = 'kredit' THEN t.amount ELSE 0 END) as income,
                SUM(CASE WHEN fa.type IN ('EX','SP') AND t.entry_type = 'debit' THEN t.amount ELSE 0 END) as expense,
                SUM(CASE 
                    WHEN fa.type = 'IN' AND t.entry_type = 'kredit' THEN t.amount
                    WHEN fa.type IN ('EX','SP') AND t.entry_type = 'debit' THEN -t.amount
                    ELSE 0 END) as surplus
            SQL)
            ->whereBetween('t.created_at', [$start, $end])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json($rows);
    }
}

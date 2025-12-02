<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyExpenseController extends Controller
{
    public function monthly(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer|min:0',
            'year'    => 'nullable|integer',
            'month'   => 'nullable|integer|min:1|max:12',
        ]);

        $year   = (int) ($request->query('year',  now()->year));
        $month  = (int) ($request->query('month', now()->month));
        $userId = $request->query('user_id'); // bisa null

        // Awal bulan (inklusif) & awal bulan berikutnya (eksklusif)
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end   = (clone $start)->addMonth();

        $periodeBulan = sprintf('%04d-%02d', $year, $month);

        $q = DB::table('transactions as t')
    ->join('user_accounts as ua', 'ua.id', '=', 't.user_account_id')
    ->join('users as u', 'u.id', '=', 'ua.id_user') // <-- pake id_user
    ->join('financial_accounts as fa', function ($join) {
        $join->on('fa.id', '=', 't.financial_account_id')
             ->where('fa.type', '=', 'EX');
    })
    ->where('t.created_at', '>=', $start)
    ->where('t.created_at', '<',  $end);

    if (!empty($userId)) {
        $q->where('ua.id_user', (int) $userId); // <-- pake id_user
    }

        $rows = $q->select([
                DB::raw('u.id as user_id'),
                DB::raw('u.name as username'),
            ])
            ->selectRaw('SUM(t.amount) as total_expenses')
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_expenses')
            ->get()
            ->map(function ($row) use ($periodeBulan) {
                return [
                    'user_id'        => (int) $row->user_id,
                    'username'       => $row->username,
                    'periode_bulan'  => $periodeBulan,
                    'total_expenses' => (int) $row->total_expenses,
                ];
            });

        return response()->json([
            'period' => [
                'year'        => $year,
                'month'       => $month,
                'start_date'  => $start->toDateString(),
                'end_date'    => $end->copy()->subDay()->toDateString(),
            ],
            'filter' => [
                'user_id' => $userId !== null && $userId !== '' ? (int)$userId : null,
            ],
            'data' => $rows,
        ]);
    }
}
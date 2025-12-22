<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

        // Panggil method dari User model
        $results = User::getMonthlyExpenses($start, $end, $userId);

        $rows = collect($results)->map(function ($row) use ($periodeBulan) {
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
            'data' => $rows,
        ]);
    }
}
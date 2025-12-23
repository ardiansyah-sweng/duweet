<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
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

        $year   = (int) ($request->query('year', now()->year));
        $month  = (int) ($request->query('month', now()->month));
        $userId = $request->query('user_id'); // optional

        // Awal bulan & awal bulan berikutnya
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end   = (clone $start)->addMonth();

        $periodeBulan = sprintf('%04d-%02d', $year, $month);

        // ✅ FINAL: ambil data dari Transaction model
        $results = Transaction::getMonthlyExpensesByUser(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            $userId
        );

        $rows = collect($results)->map(function ($row) use ($periodeBulan) {
            return [
                'user_id'        => (int) $row->user_id,
                'user_name'      => $row->username, // ✅ FIX DI SINI
                'periode_bulan'  => $periodeBulan,
                'total_expenses' => (int) $row->total_expenses,
            ];
        });

        return response()->json([
            'period' => [
                'year'       => $year,
                'month'      => $month,
                'start_date' => $start->toDateString(),
                'end_date'   => $end->copy()->subDay()->toDateString(),
            ],
            'data' => $rows,
        ]);
    }
}

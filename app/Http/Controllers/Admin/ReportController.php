<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cashout;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        // sesuaikan middleware admin dengan project Anda
        $this->middleware(['auth']);
    }

    // Form pilih periode
    public function showCashoutSumForm()
    {
        return view('admin.reports.cashout_sum_form');
    }

    // Proses summary
    public function getCashoutSumByPeriod(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date;
        $end   = $request->end_date;

        // Hitung total amount
        $total = Cashout::betweenDates($start, $end)->sum('amount');

        // Hitung breakdown per hari
        $breakdown = Cashout::select(
                DB::raw('DATE(created_at) AS date'),
                DB::raw('SUM(amount) AS total_amount'),
                DB::raw('COUNT(*) AS count_tx')
            )
            ->betweenDates($start, $end)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.reports.cashout_sum_result', compact(
            'start', 'end', 'total', 'breakdown'
        ));
    }

    // Export ke CSV
    public function exportCashoutCsv(Request $request)
    {
        $rows = Cashout::betweenDates($request->start_date, $request->end_date)
            ->orderBy('created_at')
            ->get();

        $filename = "cashout_{$request->start_date}_{$request->end_date}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User ID', 'Amount', 'Status', 'Created At']);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user_id,
                    $row->amount,
                    $row->status,
                    $row->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

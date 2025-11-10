<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; // <-- Import di sini
use Carbon\Carbon;          // <-- Import di sini

class AdminDashboardController extends Controller
{
    // Taruh fungsimu DI DALAM class ini
    public function showExpenseReport(Request $request)
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subMonths(3)->startOfDay();

        $expenses = Transaction::getExpensesByPeriodRaw($startDate, $endDate);
        
        //return view('admin.reports.expenses', compact('expenses', 'startDate'));
        dd($expenses);
    }
}
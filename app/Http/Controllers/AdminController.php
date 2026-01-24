<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Admin;

class AdminController extends Controller
{
    public function SumCashOutByPeriod(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $totalCashOut = Admin::SumCashOutByPeriod($startDate, $endDate);

        return response()->json([
            'success' => true,
            'total_cash_out' => $totalCashOut
        ]);
    }
}
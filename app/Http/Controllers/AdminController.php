<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class AdminController extends Controller
{
    /**
     * Get income report by period
     * 
     * GET /api/admin/income/by-period
     * 
     * Query Parameters:
     * - period: daily|weekly|monthly|yearly (default: monthly)
     * - start_date: Y-m-d format (optional)
     * - end_date: Y-m-d format (optional)
     * - user_id: integer (optional)
     */
    public function getIncomeByPeriod(Request $request)
    {
        $validated = $request->validate([
            'period' => 'in:daily,weekly,monthly,yearly',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $period = $validated['period'] ?? 'monthly';
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $userId = $validated['user_id'] ?? null;

        $data = Transaction::sumIncomeByPeriod($period, $startDate, $endDate, $userId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'filters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
            ],
        ]);
    }

    /**
     * Get income report by category
     * 
     * GET /api/admin/income/by-category
     * 
     * Query Parameters:
     * - start_date: Y-m-d format (optional)
     * - end_date: Y-m-d format (optional)
     * - user_id: integer (optional)
     */
    public function getIncomeByCategory(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $userId = $validated['user_id'] ?? null;

        $data = Transaction::sumIncomeByCategory($startDate, $endDate, $userId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
            ],
        ]);
    }

    /**
     * Get income summary
     * 
     * GET /api/admin/income/summary
     * 
     * Query Parameters:
     * - start_date: Y-m-d format (optional)
     * - end_date: Y-m-d format (optional)
     * - user_id: integer (optional)
     */
    public function getIncomeSummary(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $userId = $validated['user_id'] ?? null;

        $summary = Transaction::getIncomeSummary($startDate, $endDate, $userId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get comprehensive income report
     * 
     * GET /api/admin/income/report
     * 
     * Query Parameters:
     * - period: daily|weekly|monthly|yearly (default: monthly)
     * - start_date: Y-m-d format (optional)
     * - end_date: Y-m-d format (optional)
     * - user_id: integer (optional)
     */
    public function getIncomeReport(Request $request)
    {
        $validated = $request->validate([
            'period' => 'in:daily,weekly,monthly,yearly',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $period = $validated['period'] ?? 'monthly';
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $userId = $validated['user_id'] ?? null;

        $byPeriod = Transaction::sumIncomeByPeriod($period, $startDate, $endDate, $userId);
        $byCategory = Transaction::sumIncomeByCategory($startDate, $endDate, $userId);
        $summary = Transaction::getIncomeSummary($startDate, $endDate, $userId);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'by_period' => $byPeriod,
                'by_category' => $byCategory,
            ],
            'filters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
            ],
        ]);
    }
}

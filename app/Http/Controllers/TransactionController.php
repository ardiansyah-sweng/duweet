<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Get all transactions with optional filters using raw SQL
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Use raw SQL query method
            $transactions = Transaction::getAllTransactions(
                $request->input('user_account_id'),
                $request->input('financial_account_id'),
                $request->input('entry_type')
            );

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'count' => $transactions->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter transactions by date range (period) using raw SQL
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function filterByPeriod(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date|date_format:Y-m-d',
                'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
                'user_account_id' => 'nullable|integer',
                'financial_account_id' => 'nullable|integer',
                'entry_type' => 'nullable|in:debit,credit'
            ]);

            // Use raw SQL query method
            $transactions = Transaction::filterTransactionsByPeriod(
                $request->start_date,
                $request->end_date,
                $request->input('user_account_id'),
                $request->input('financial_account_id'),
                $request->input('entry_type')
            );

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'count' => $transactions->count(),
                'period' => [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal filter transaksi berdasarkan periode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail transaksi berdasarkan ID
     */
    public function show($id)
    {
        $result = Transaction::getDetailById($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

     public function monthlyExpense(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'nullable|integer|min:1',
            'year'    => 'nullable|integer',
            'month'   => 'nullable|integer|min:1|max:12',
        ]);

        $year   = (int) ($request->query('year', now()->year));
        $month  = (int) ($request->query('month', now()->month));
        $userId = $request->query('user_id');

        // Awal bulan & awal bulan berikutnya
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end   = (clone $start)->addMonth();

        $periodeBulan = sprintf('%04d-%02d', $year, $month);

        // ✅ RAW SQL dari Model
        $results = Transaction::getMonthlyExpensesByUser(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            $userId
        );

        $rows = collect($results)->map(function ($row) use ($periodeBulan) {
            return [
                'user_id'        => (int) $row->user_id,
                'user_name'      => $row->username,
                'periode_bulan'  => $periodeBulan,
                'total_expenses' => (int) $row->total_expenses,
            ];
        });

        return response()->json([
            'success' => true,
            'period' => [
                'year'       => $year,
                'month'      => $month,
                'start_date' => $start->toDateString(),
                'end_date'   => $end->copy()->subDay()->toDateString(),
            ],
            'data' => $rows,
            ]);
    }
    public function getLatestActivities()
    {
        $activities = Transaction::getLatestActivitiesRaw();
        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Hard delete transaksi berdasarkan transaction_group_id.
     */
    public function hardDeleteByGroupId($groupId)
    {
        $result = Transaction::hardDeleteByGroupId($groupId);

        $status = $result['success'] ? 200 : 400;
        return response()->json($result, $status);
    }

    /**
     * Update transaksi existing (amount, description, created_at)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'amount' => 'nullable|integer|min:0',
                'description' => 'nullable|string|max:1000',
                'created_at' => 'nullable|date_format:Y-m-d H:i:s',
            ]);

            // Panggil method dari model
            $result = Transaction::updateTransaction((int) $id, $validated);

            $status = $result['success'] ? 200 : 400;
            return response()->json($result, $status);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}

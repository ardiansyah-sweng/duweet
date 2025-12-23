<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
}

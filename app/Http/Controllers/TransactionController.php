<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Get all transactions with optional filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Transaction::query();

            // Filter by user account (optional)
            if ($request->has('user_account_id')) {
                $query->byUserAccount($request->user_account_id);
            }

            // Filter by financial account (optional)
            if ($request->has('financial_account_id')) {
                $query->byFinancialAccount($request->financial_account_id);
            }

            // Filter by entry type (optional)
            if ($request->has('entry_type')) {
                $query->byEntryType($request->entry_type);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

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
     * Filter transactions by date range (period)
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

            $query = Transaction::query()
                ->byPeriod($request->start_date, $request->end_date);

            // Optional filters
            if ($request->has('user_account_id')) {
                $query->byUserAccount($request->user_account_id);
            }

            if ($request->has('financial_account_id')) {
                $query->byFinancialAccount($request->financial_account_id);
            }

            if ($request->has('entry_type')) {
                $query->byEntryType($request->entry_type);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

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
     * Filter transactions by month and year
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function filterByMonth(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
                'user_account_id' => 'nullable|integer',
                'financial_account_id' => 'nullable|integer',
                'entry_type' => 'nullable|in:debit,credit'
            ]);

            $query = Transaction::query()
                ->byMonth($request->month, $request->year);

            // Optional filters
            if ($request->has('user_account_id')) {
                $query->byUserAccount($request->user_account_id);
            }

            if ($request->has('financial_account_id')) {
                $query->byFinancialAccount($request->financial_account_id);
            }

            if ($request->has('entry_type')) {
                $query->byEntryType($request->entry_type);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'count' => $transactions->count(),
                'period' => [
                    'month' => $request->month,
                    'year' => $request->year
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
                'message' => 'Gagal filter transaksi berdasarkan bulan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter transactions by year
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function filterByYear(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'year' => 'required|integer|min:2000|max:2100',
                'user_account_id' => 'nullable|integer',
                'financial_account_id' => 'nullable|integer',
                'entry_type' => 'nullable|in:debit,credit'
            ]);

            $query = Transaction::query()
                ->byYear($request->year);

            // Optional filters
            if ($request->has('user_account_id')) {
                $query->byUserAccount($request->user_account_id);
            }

            if ($request->has('financial_account_id')) {
                $query->byFinancialAccount($request->financial_account_id);
            }

            if ($request->has('entry_type')) {
                $query->byEntryType($request->entry_type);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'count' => $transactions->count(),
                'period' => [
                    'year' => $request->year
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
                'message' => 'Gagal filter transaksi berdasarkan tahun: ' . $e->getMessage()
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

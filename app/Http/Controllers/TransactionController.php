<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $transactions = Transaction::getAllTransactions(
                $request->input('user_account_id'),
                $request->input('financial_account_id'),
                $request->input('entry_type')
            );

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'count' => $transactions->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function byUserAccount(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_account_id' => 'required|integer',
                'start_date' => 'nullable|date|date_format:Y-m-d',
                'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            ]);

            $transactions = Transaction::getTransactionsByUserAccount(
                (int) $request->input('user_account_id'),
                $request->input('start_date'),
                $request->input('end_date')
            );

            return response()->json([
                'success' => true,
                'filters' => [
                    'user_account_id' => (int) $request->input('user_account_id'),
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                ],
                'count' => $transactions->count(),
                'data' => $transactions,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal filter transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function filterByPeriod(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date|date_format:Y-m-d',
                'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
                'user_account_id' => 'nullable|integer',
                'financial_account_id' => 'nullable|integer',
                'entry_type' => 'nullable|in:debit,credit',
            ]);

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
                    'end_date' => $request->end_date,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal filter transaksi berdasarkan periode: ' . $e->getMessage(),
            ], 500);
        }
    }

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
            'year' => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year = (int) ($request->query('year', now()->year));
        $month = (int) ($request->query('month', now()->month));
        $userId = $request->query('user_id');

        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = (clone $start)->addMonth();

        $periodeBulan = sprintf('%04d-%02d', $year, $month);

        /** @var \Illuminate\Support\Collection<int, \stdClass|array> $results */
        $results = Transaction::getMonthlyExpensesByUser(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            $userId
        );

        $rows = collect($results)->map(function ($row) use ($periodeBulan) {
            $r = is_array($row) ? (object) $row : $row;

            return [
                'user_id' => (int) ($r->user_id ?? 0),
                'user_name' => $r->username ?? null,
                'periode_bulan' => $periodeBulan,
                'total_expenses' => (int) ($r->total_expenses ?? 0),
            ];
        });

        return response()->json([
            'success' => true,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $start->toDateString(),
                'end_date' => $end->copy()->subDay()->toDateString(),
            ],
            'data' => $rows,
        ]);
    }

    public function getLatestActivities()
    {
        $activities = Transaction::getLatestActivitiesRaw();

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    public function hardDeleteByGroupId($groupId)
    {
        $result = Transaction::hardDeleteByGroupId($groupId);

        $status = $result['success'] ? 200 : 400;

        return response()->json($result, $status);
    }

    /**
     * Update existing transaction
     * 
     * @param Request $request
     * @param int $id Transaction ID
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Validasi input (Hanya Description & Transaction Date sesuai tugas)
            $validated = $request->validate([
                'description' => 'nullable|string|max:1000',
                'transaction_date' => 'nullable|date',
            ]);

            // Cek apakah ada data yang akan diupdate
            if (empty($validated)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang akan diupdate',
                ], 400);
            }

            // Panggil method update di model
            $updated = Transaction::updateTransaction((int) $id, $validated);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate transaksi atau tidak ada perubahan data',
                ], 400);
            }

            // Ambil data transaksi yang sudah diupdate
            $transaction = Transaction::getDetailById($id);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate',
                'data' => $transaction,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
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

    public function Insert(Request $request)
    {
        $validated = $request->validate([
            'user_account_id' => 'required|integer',
            'financial_account_id' => 'required|integer',
            'entry_type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date|date_format:Y-m-d H:i:s',
        ]);

       
        $validated['balance_effect'] = $validated['entry_type'] === 'debit' ? 'decrease' : 'increase';
        $validated['is_balance'] = $request->boolean('is_balance', false);

        try {
            $transactionId = Transaction::insertTransactionRaw($validated);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'transaction_id' => $transactionId
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        // Validasi: hanya description dan date, boleh salah satu (nullable)
        $validated = $request->validate([
            'transaction_date' => 'nullable|date|date_format:Y-m-d H:i:s',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            // Filter hanya data yang tidak null
            $updateData = array_filter($validated, function ($value) {
                return !is_null($value);
            });

            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dikirim untuk diperbarui.',
                ], 400);
            }

            // Panggil method model updateDateDescription
            $affected = Transaction::updateDateDescription((int) $id, $updateData);

            if ($affected > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil diperbarui.',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan atau tidak ada perubahan data.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Get spending summary (sum of spending) grouped by period for a user account
     * Query params (GET): user_account_id (int), start_date (Y-m-d), end_date (Y-m-d)
     */
    public function spendingSummaryByPeriod(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_account_id' => 'required|integer',
                'start_date' => 'required|date|date_format:Y-m-d',
                'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            ]);

            $start = Carbon::parse($request->query('start_date'))->startOfDay();
            $end = Carbon::parse($request->query('end_date'))->endOfDay();

            $results = Transaction::getSpendingSummaryByPeriod(
                (int) $request->query('user_account_id'),
                $start,
                $end
            );

            $data = collect($results)->map(function ($row) {
                return [
                    'period' => $row->periode,
                    'total_spending' => (int) $row->total_spending,
                ];
            })->values();

            $payload = [
                'success' => true,
                'period' => [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                ],
                'data' => $data,
            ];

            return response()->json($payload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
                } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil spending summary: ' . $e->getMessage()
            ], 500);
        }
    } 
    public function destroy($id)
    {
    try {
        // Memanggil fungsi deleteByIdRaw yang tadi dibuat
        $deleted = Transaction::deleteByGroupIdRaw($id);

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dihapus'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'keyword' => 'required|string|min:3',
            ]);

            $keyword = $request->input('keyword');

           
            $results = Transaction::fullTextSearchDescription($keyword);

            return response()->json([
                'success' => true,
                'keyword' => $keyword,
                'count' => $results->count(),
                'data' => $results,
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
                'message' => 'Gagal melakukan pencarian: ' . $e->getMessage(),
            ], 500);
        }
    }

}
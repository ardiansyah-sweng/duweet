<?php

namespace App\Http\Controllers;

use App\Constants\TransactionColumns;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display all transactions with optional filters
     * 
     * Query Parameters:
     * - user_account_id: Filter by user account
     * - financial_account_id: Filter by financial account
     * - entry_type: Filter by entry type (debit/credit)
     * - transaction_group_id: Filter by transaction group
     * - start_date: Filter by start date (Y-m-d)
     * - end_date: Filter by end date (Y-m-d)
     * - exclude_balance: Exclude balance transactions (true/false)
     * - balance_only: Show only balance transactions (true/false)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with(['userAccount', 'financialAccount']);

        // Filter by user account
        if ($request->has('user_account_id')) {
            $query->byUserAccount($request->user_account_id);
        }

        // Filter by financial account
        if ($request->has('financial_account_id')) {
            $query->byFinancialAccount($request->financial_account_id);
        }

        // Filter by entry type
        if ($request->has('entry_type')) {
            $query->byEntryType($request->entry_type);
        }

        // Filter by transaction group
        if ($request->has('transaction_group_id')) {
            $query->byTransactionGroup($request->transaction_group_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter balance transactions
        if ($request->boolean('balance_only')) {
            $query->balanceOnly();
        } elseif ($request->boolean('exclude_balance')) {
            $query->excludeBalance();
        }

        // Order by created_at descending
        $query->orderBy(TransactionColumns::CREATED_AT, 'desc');

        $transactions = $query->get();

        return response()->json([
            'success' => true,
            'total' => $transactions->count(),
            'filters_applied' => array_filter([
                'user_account_id' => $request->user_account_id,
                'financial_account_id' => $request->financial_account_id,
                'entry_type' => $request->entry_type,
                'transaction_group_id' => $request->transaction_group_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'balance_only' => $request->boolean('balance_only'),
                'exclude_balance' => $request->boolean('exclude_balance'),
            ]),
            'data' => $transactions
        ]);
    }

    /**
     * Filter transactions by date range (period) using raw SQL
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
     * Display a specific transaction
     */
    public function show($id): JsonResponse
    {
        $transaction = Transaction::with(['userAccount', 'financialAccount'])->find($id);
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    /**
     * Get transactions by user account (dedicated endpoint)
     */
    public function getByUserAccount($userAccountId): JsonResponse
    {
        $transactions = Transaction::getTransactionsByUserAccount($userAccountId);

        return response()->json([
            'success' => true,
            'user_account_id' => $userAccountId,
            'total' => $transactions->count(),
            'data' => $transactions
        ]);
    }

    /**
     * Get transactions by financial account (dedicated endpoint)
     */
    public function getByFinancialAccount($financialAccountId): JsonResponse
    {
        $transactions = Transaction::with(['userAccount', 'financialAccount'])
            ->byFinancialAccount($financialAccountId)
            ->orderBy(TransactionColumns::CREATED_AT, 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'financial_account_id' => $financialAccountId,
            'total' => $transactions->count(),
            'data' => $transactions
        ]);
    }

    /**
     * Get transactions by transaction group
     */
    public function getByTransactionGroup($groupId): JsonResponse
    {
        $transactions = Transaction::with(['userAccount', 'financialAccount'])
            ->byTransactionGroup($groupId)
            ->orderBy(TransactionColumns::CREATED_AT, 'asc')
            ->get();

        // Calculate total debit and credit
        $totalDebit = $transactions->where(TransactionColumns::ENTRY_TYPE, 'debit')
            ->sum(TransactionColumns::AMOUNT);
        $totalCredit = $transactions->where(TransactionColumns::ENTRY_TYPE, 'credit')
            ->sum(TransactionColumns::AMOUNT);

        return response()->json([
            'success' => true,
            'transaction_group_id' => $groupId,
            'total_transactions' => $transactions->count(),
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => $totalDebit === $totalCredit,
            'data' => $transactions
        ]);
    }

    /**
     * Get transaction statistics by user account
     */
    public function getStatsByUserAccount($userAccountId): JsonResponse
    {
        $transactions = Transaction::byUserAccount($userAccountId)->get();

        $totalDebit = $transactions->where(TransactionColumns::ENTRY_TYPE, 'debit')
            ->sum(TransactionColumns::AMOUNT);
        $totalCredit = $transactions->where(TransactionColumns::ENTRY_TYPE, 'credit')
            ->sum(TransactionColumns::AMOUNT);

        return response()->json([
            'success' => true,
            'user_account_id' => $userAccountId,
            'statistics' => [
                'total_transactions' => $transactions->count(),
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'debit_count' => $transactions->where(TransactionColumns::ENTRY_TYPE, 'debit')->count(),
                'credit_count' => $transactions->where(TransactionColumns::ENTRY_TYPE, 'credit')->count(),
                'balance_transactions' => $transactions->where(TransactionColumns::IS_BALANCE, true)->count(),
            ]
        ]);
    }

    /**
     * Store a new transaction
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            TransactionColumns::USER_ACCOUNT_ID => 'required|exists:user_accounts,id',
            TransactionColumns::FINANCIAL_ACCOUNT_ID => 'required|exists:financial_accounts,id',
            TransactionColumns::TRANSACTION_GROUP_ID => 'required|string|max:36',
            TransactionColumns::ENTRY_TYPE => 'required|in:debit,credit',
            TransactionColumns::AMOUNT => 'required|integer|min:0',
            TransactionColumns::BALANCE_EFFECT => 'required|in:increase,decrease',
            TransactionColumns::DESCRIPTION => 'nullable|string',
            TransactionColumns::IS_BALANCE => 'boolean',
        ]);

        $transaction = Transaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data' => $transaction
        ], 201);
    }

    /**
     * Update a transaction
     */
    public function update(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            TransactionColumns::DESCRIPTION => 'nullable|string',
            TransactionColumns::AMOUNT => 'sometimes|integer|min:0',
        ]);

        $transaction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diupdate',
            'data' => $transaction
        ]);
    }

    /**
     * Delete a transaction
     */
    public function destroy($id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
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

        $results = Transaction::getMonthlyExpensesByUser(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            $userId
        );

        $rows = collect($results)->map(function (\stdClass $row) use ($periodeBulan) {
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
}

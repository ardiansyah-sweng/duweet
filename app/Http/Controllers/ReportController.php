<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Http\Request;
use App\Models\UserAccount; // Asumsi Model ini ada atau dibuat
use App\Models\Transaction; // <-- PENTING: Import Model Transaction
use App\Constants\TransactionColumns;
use Carbon\Carbon;


use App\Constants\UserColumns;
use App\Constants\AccountColumns;
use App\Constants\FinancialAccountColumns;
use App\Constants\UserFinancialAccountColumns;

class ReportController extends Controller
{
    /**
     * Mengambil ringkasan total pendapatan (Income) per bulan dengan metadata lengkap
     */
    public function incomeSummary(Request $request)
    {
        // 1. Ambil data dasar pengguna dan akun (Thin Controller)
        $baseData = $this->getReportBaseData($request);
    
        if ($baseData instanceof \Illuminate\Http\JsonResponse) {
            return $baseData; // Mengembalikan error jika user/account tidak ditemukan
        }
    
        ['user' => $user, 'userAccount' => $userAccount, 'userData' => $userData, 'userAccountData' => $userAccountData] = $baseData;
        $userAccountId = $userAccount->id;
    
        // 2. Penanganan Periode
        $defaultStartDate = Carbon::create(2025, 1, 1)->startOfDay();
        $defaultEndDate = Carbon::create(2025, 12, 31)->endOfDay();
    
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : $defaultStartDate;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $defaultEndDate;
    
        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'], 400);
        }
    
        // 3. Panggil Logika Query dari Model
        try {
            $summary = Transaction::getIncomeSummaryByPeriod(
                $userAccountId,
                $startDate,
                $endDate
            );
    
            // 4. Format response dengan metadata lengkap
            $response = [
                'user' => $userData,
                'user_account' => $userAccountData,
                'summary' => $summary,
            ];
    
            return response()->json($response);
    
        } catch (\Exception $e) {
            // Tangani error database/query
            return response()->json([
                'error' => 'Gagal mengambil ringkasan transaksi.',
                'message' => $e->getMessage(),
                'hint' => 'Periksa apakah Model Transaction telah dikonfigurasi dengan benar.'
            ], 500);
        }
    }

    /**
     * Ringkasan surplus/defisit per periode (ADMIN: agregat seluruh user).
     *
     * GET /api/reports/surplus-deficit
     * Query params:
     * - start_date (optional, format: Y-m-d)
     * - end_date   (optional, format: Y-m-d)
     */
    public function surplusDeficitByPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $defaultStartDate = Carbon::create(2025, 1, 1)->startOfDay();
        $defaultEndDate = Carbon::create(2025, 12, 31)->endOfDay();

        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : $defaultStartDate;

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : $defaultEndDate;

        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.',
            ], 400);
        }

        try {
            $summary = Transaction::getSurplusDeficitSummaryByPeriod($startDate, $endDate);

            return response()->json([
                'success' => true,
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan surplus/defisit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Helper privat untuk mengambil data dasar User dan User Account.
     * Mengurangi duplikasi kode di dalam controller.
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    private function getReportBaseData(Request $request)
    {
        // Terima filter opsional dari query string
        $email = $request->query('email');
        $userId = $request->query('user_id');
        $userAccountId = $request->query('user_account_id');
    
        // Temukan user_account jika diberikan langsung
        if ($userAccountId) {
            $userAccount = DB::table('user_accounts')->where('id', (int) $userAccountId)->first();
            if (!$userAccount) {
                return response()->json(['error' => 'User account not found by provided user_account_id.'], 404);
            }
            $user = User::find($userAccount->id_user);
            if (!$user) {
                return response()->json(['error' => 'User not found for the given user_account.'], 404);
            }
        } else {
            // Temukan user berdasarkan email, user_id, atau fallback ke user pertama
            if ($email) {
                $user = User::where('email', $email)->first();
            } elseif ($userId) {
                $user = User::find((int) $userId);
            } else {
                $user = User::first();
            }
        }
    
        if (!$user) {
            return response()->json(['error' => 'User not found. Provide email, user_id, or create users via seeder.'], 404);
        }
    
        // Ambil user account terkait hanya jika belum ditemukan dari user_account_id param
        if (!isset($userAccount) || $userAccount === null) {
            $userAccount = DB::table('user_accounts')->where('id_user', $user->id)->first();
            
            // Fallback: jika user tidak punya user_accounts, gunakan user_account pertama yang tersedia
            if (!$userAccount) {
                $userAccount = DB::table('user_accounts')->first();
                if (!$userAccount) {
                    return response()->json(['error' => 'No user accounts found in the system. Please seed the database first.'], 404);
                }
                // Update user ke pemilik user_account tersebut
                $user = User::find($userAccount->id_user);
            }
        }
    
        if (!$userAccount) {
            return response()->json(['error' => 'User account configuration not found for user ID ' . $user->id . '.'], 404);
        }
    
        // Siapkan data user untuk metadata
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'provinsi' => $user->provinsi,
            'kabupaten' => $user->kabupaten,
        ];
        
        // Siapkan data user_account untuk metadata
        $userAccountData = [
            'id' => $userAccount->id,
            'username' => $userAccount->username,
            'email' => $userAccount->email,
        ];
    
        return compact('user', 'userAccount', 'userData', 'userAccountData');
    }
    private function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    /**
     * Return total transactions per user account as JSON.
     *
     * GET /api/reports/transactions-per-user-account
     * Query parameters:
     * - user_account_id: Filter by specific user account (optional)
     * 
     * Returns:
     * - user_account_id: User account ID
     * - user_account_email: User account email
     * - transaction_count: Count of unique transaction groups (COUNT DISTINCT transaction_group_id)
     */
    public function getTotalTransactionsPerUserAccount(Request $request)
    {
        // Validate optional parameter
        $validator = Validator::make($request->all(), [
            'user_account_id' => 'nullable|integer|exists:user_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $userAccountId = $request->query('user_account_id');

        // Get transaction totals per user account from model
        $data = Transaction::getTotalTransactionsPerUserAccount($userAccountId);

        return response()->json([
            'status' => 'success',
            'filter' => [
                'user_account_id' => $userAccountId,
            ],
            'count' => $data->count(),
            'data' => $data,
        ]);
    }
    
    public function adminSpendingSummary(Request $request)
    {
        // 1. Ambil periode (query params)
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // 2. Validasi periode
        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            ], 400);
        }

        // 3. Panggil Model (ADMIN REPORT)
        try {
            $data = Transaction::getTotalSpendingByUserAccountAdmin(
                $startDate,
                $endDate
            );

            return response()->json([
                'success' => true,
                'type' => 'ADMIN_SPENDING_REPORT',
                'period' => [
                    'from' => $startDate->toDateString(),
                    'to'   => $endDate->toDateString(),
                ],
                'total_user_accounts' => $data->count(),
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan pengeluaran admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**

     * ADMIN REPORT
     * Get expenses summary by period (month) for all users
     * 
     * GET /api/admin/reports/expenses-summary
     * Query parameters:
     * - start_date: optional (default: start of current month)
     * - end_date: optional (default: end of current month)
     */
    public function adminExpensesSummary(Request $request)
    {
        // 1. Get period from query params
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // 2. Validate period
        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            ], 400);
        }

        // 3. Call Model (ADMIN REPORT)
        try {
            $data = Transaction::getExpensesSummaryByPeriodAdmin(
                $startDate,
                $endDate
            );

            return response()->json([
                'success' => true,
                'type' => 'ADMIN_EXPENSES_REPORT',
                'period' => [
                    'from' => $startDate->toDateString(),
                    'to'   => $endDate->toDateString(),
                ],
                'total_periods' => $data->count(),
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan expenses admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sum of all users' financial accounts grouped by type
     */
    public function sumFinancialAccountsByType()
    {
        $result = UserFinancialAccount::sumAllUsersFinancialAccountsByType();
        return response()->json($result);
    }
    
    
        /**
     * Surplus / Defisit user berdasarkan periode
     *
     * GET /api/reports/surplus-defisit
     */
    public function surplusDefisitByPeriod(Request $request)
    {
        // 1. Ambil data dasar user & user account
        $baseData = $this->getReportBaseData($request);

        if ($baseData instanceof \Illuminate\Http\JsonResponse) {
            return $baseData;
        }

        ['user' => $user, 'userAccount' => $userAccount, 'userData' => $userData, 'userAccountData' => $userAccountData] = $baseData;

        // 2. Periode
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
            ], 400);
        }

        // 3. Query ke model
        $summary = Transaction::getSurplusDefisitByPeriod(
            $userAccount->id,
            $startDate,
            $endDate
        );

        // 4. Response
        return response()->json([
            'user' => $userData,
            'user_account' => $userAccountData,
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => $summary
        ]);
    }

    /**
     * ADMIN REPORT
     * Sum cash-in grouped by period for admin view
     * GET /api/admin/reports/cashin-by-period
     */
    public function adminCashinByPeriod(Request $request)
    {
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
            ], 400);
        }

        try {
            // Query using transaction_date to include backdated transactions created by factories
            $transactionsTable = config('db_tables.transaction', 'transactions');
            $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

            try {
                $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            } catch (\Exception $e) {
                $driver = 'mysql';
            }

            if ($driver === 'sqlite') {
                $periodeExpr = "strftime('%Y-%m', t.transaction_date)";
            } elseif ($driver === 'pgsql' || $driver === 'postgres') {
                $periodeExpr = "to_char(t.transaction_date, 'YYYY-MM')";
            } else {
                $periodeExpr = "DATE_FORMAT(t.transaction_date, '%Y-%m')"; // MySQL/MariaDB
            }

            $sql = "
                SELECT
                    {$periodeExpr} AS periode,
                    COALESCE(SUM(t.amount), 0) AS total_cashin
                FROM {$transactionsTable} t
                INNER JOIN {$financialAccountsTable} fa ON fa.id = t.financial_account_id
                WHERE
                    fa.type = 'IN'
                    AND fa.is_group = 0
                    AND t.transaction_date BETWEEN ? AND ?
                GROUP BY {$periodeExpr}
                ORDER BY periode ASC
            ";

            $rows = DB::select($sql, [
                $startDate->toDateTimeString(),
                $endDate->toDateTimeString(),
            ]);

            $data = collect($rows);

            $grandTotal = (int) $data->sum('total_cashin');

            // filter metadata
            $filterUserAccount = $request->query('user_account_id') ?? 'all';

            // monthly_breakdown removed per request; response will only include totals

            return response()->json([
                'success' => true,
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'filter' => [
                    'user_account_id' => $filterUserAccount,
                ],
                'total_cash_in' => $grandTotal,
                'total_cash_in_formatted' => (new self)->rupiah($grandTotal),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan cash-in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /* Sum Cash In by Period
     * 
     * GET /api/reports/sum-cashin-by-period
     * 
     * Query Params:
     * - start_date (required, format: Y-m-d)
     * - end_date (optional, format: Y-m-d) - jika kosong, auto-set berdasarkan period_format
     * - period_format (optional, default: month) - day|week|month|quarter|year
     * - user_account_id (optional)
     * - financial_account_id (optional)
     * 
     * Returns: Total cash in grouped by periode dan akun
     */
    public function sumCashInByPeriod(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'user_account_id' => 'nullable|integer|exists:user_accounts,id',
            'financial_account_id' => 'nullable|integer|exists:financial_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Parse start_date (required)
        $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
        // Period format will be determined solely from the input date range below
        $periodFormat = 'month';

        // 3. Auto-set end_date berdasarkan period_format jika tidak diberikan
        if ($request->query('end_date')) {
            $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
        } else {
            // Default end_date = end of month from start date
            $endDate = $startDate->copy()->endOfMonth()->endOfDay();
        }

        // 4. Validasi periode
        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
            ], 400);
        }

        // 6. Extract filter parameters
        $userAccountId = $request->query('user_account_id') ? (int) $request->query('user_account_id') : null;
        $financialAccountId = $request->query('financial_account_id') ? (int) $request->query('financial_account_id') : null;

        try {
            // 7. Query ke model dengan opsi filter (no period grouping)
            $data = Transaction::sumCashInByPeriod(
                $startDate,
                $endDate,
                $userAccountId,
                $financialAccountId
            );

            // 8. Calculate aggregate totals
            $totalCashIn = $data->sum('total_cash_in');
            $totalTransactions = $data->sum('transaction_count');

            // 9. Format response
            return response()->json([
                'status' => 'success',
                'type' => 'CASHIN_SUMMARY_REPORT',
                'summary' => [
                    'total_cash_in' => $totalCashIn,
                    'total_transactions' => $totalTransactions,
                ],
                'period' => [
                    'from' => $startDate->toDateString(),
                    'to' => $endDate->toDateString(),
                ],
                'filters' => [
                    'user_account_id' => $userAccountId,
                    'financial_account_id' => $financialAccountId,
                    'account_type' => 'AS',
                ],
                'total_records' => $data->count(),
                'data' => $data,
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil laporan sum cash in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

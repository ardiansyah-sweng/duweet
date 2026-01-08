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
     * Sum Cash In (credit transactions) by period
     * 
     * GET /api/reports/sum-cash-in-by-period
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cashInByPeriod(Request $request)
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_account_id' => 'nullable|integer|exists:user_accounts,id',
            'financial_account_id' => 'nullable|integer|exists:financial_accounts,id',
            'period_format' => 'nullable|in:day,week,month,quarter,year',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get period parameters
            $startDate = $request->input('start_date')
                ? Carbon::parse($request->input('start_date'))->startOfDay()
                : Carbon::now()->startOfMonth();

            $endDate = $request->input('end_date')
                ? Carbon::parse($request->input('end_date'))->endOfDay()
                : Carbon::now()->endOfMonth();

            // Validate date range
            if ($startDate->greaterThan($endDate)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
                ], 400);
            }

            // Get optional filters
            $userAccountId = $request->input('user_account_id');
            $financialAccountId = $request->input('financial_account_id');
            $periodFormat = $request->input('period_format', 'month');

            // Query data from model
            $data = Transaction::sumCashInByPeriod(
                $startDate,
                $endDate,
                $userAccountId,
                $financialAccountId,
                $periodFormat
            );

            // Calculate summary statistics
            $totalCashIn = $data->sum('total_cash_in');
            $transactionCount = $data->sum('transaction_count');
            $recordCount = $data->count();

            // Format response
            return response()->json([
                'status' => 'success',
                'type' => 'CASH_IN_BY_PERIOD_REPORT',
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'format' => $periodFormat,
                ],
                'filters' => [
                    'user_account_id' => $userAccountId,
                    'financial_account_id' => $financialAccountId,
                ],
                'summary' => [
                    'total_cash_in' => $totalCashIn,
                    'total_transactions' => $transactionCount,
                    'period_records' => $recordCount,
                ],
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil laporan cash in by period',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

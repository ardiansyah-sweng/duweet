<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserAccount; // Asumsi Model ini ada atau dibuat
use App\Constants\TransactionColumns;
use Carbon\Carbon;

/**
 * ================================================================
 * DML SQL DOCUMENTATION - QUERY SUM INCOME BY PERIOD
 * ================================================================
 * 
 * 1. INSERT USERS (3 users)
 * ================================================================
 * INSERT INTO users 
 * (name, first_name, last_name, email, provinsi, kabupaten, kecamatan, jalan, kode_pos, tanggal_lahir, bulan_lahir, tahun_lahir, usia, created_at, updated_at)
 * VALUES
 * ('Demo User', 'Demo', 'User', 'demo_full@duweet.com', 'Jakarta', 'Jakarta Pusat', 'Menteng', 'Jl. Demo No. 1', '10310', 1, 1, 1990, 34, NOW(), NOW()),
 * ('Test User 2', 'Test', 'User Two', 'user2@example.com', 'Jawa Barat', 'Bandung', 'Cicendo', 'Jl. Test No. 2', '40175', 15, 6, 1992, 32, NOW(), NOW()),
 * ('Test User 3', 'Test', 'User Three', 'user3@example.com', 'Jawa Timur', 'Surabaya', 'Tegalsari', 'Jl. Test No. 3', '60123', 28, 12, 1988, 36, NOW(), NOW());
 * 
 * 2. INSERT USER_ACCOUNTS (4 akun)
 * ================================================================
 * INSERT INTO user_accounts 
 * (id_user, username, email, created_at, updated_at)
 * VALUES
 * (1, 'lillie39', 'demo_full@duweet.com', NOW(), NOW()),
 * (2, 'stehr.darwin', 'pfannerstill.troy@example.net', NOW(), NOW()),
 * (2, 'moises46', 'zmarvin@example.com', NOW(), NOW()),
 * (3, 'pfisher', 'hmayert@example.org', NOW(), NOW());
 * 
 * 3. INSERT FINANCIAL_ACCOUNTS (2 akun: Income & Expense)
 * ================================================================
 * INSERT INTO financial_accounts 
 * (name, type, balance, initial_balance, is_group, is_active, level, created_at, updated_at)
 * VALUES
 * ('Gaji Bulanan', 'IN', 0, 0, 0, 1, 0, NOW(), NOW()),
 * ('Biaya Sewa / Cicilan', 'EX', 0, 0, 0, 1, 0, NOW(), NOW());
 * 
 * 4. INSERT TRANSACTIONS (96 transaksi: 12 bulan x 2 tipe x 4 user_accounts)
 * ================================================================
 * -- Income: Gaji Bulanan (8 juta, Mei: 13 juta = 8M + 5M bonus)
 * INSERT INTO transactions 
 * (user_account_id, financial_account_id, transaction_group_id, entry_type, amount, balance_effect, description, is_balance, created_at, updated_at)
 * VALUES
 * (1, 1, UUID(), 'credit', 8000000, 'increase', 'Gaji Bulanan Jan 2025', 1, '2025-01-05', '2025-01-05'),
 * (1, 1, UUID(), 'credit', 8000000, 'increase', 'Gaji Bulanan Feb 2025', 1, '2025-02-05', '2025-02-05'),
 * (1, 1, UUID(), 'credit', 8000000, 'increase', 'Gaji Bulanan Mar 2025', 1, '2025-03-05', '2025-03-05'),
 * (1, 1, UUID(), 'credit', 8000000, 'increase', 'Gaji Bulanan Apr 2025', 1, '2025-04-05', '2025-04-05'),
 * (1, 1, UUID(), 'credit', 13000000, 'increase', 'Gaji Bulanan May 2025', 1, '2025-05-05', '2025-05-05'), -- +5M bonus
 * (1, 1, UUID(), 'credit', 8000000, 'increase', 'Gaji Bulanan Jun 2025', 1, '2025-06-05', '2025-06-05'),
 * ... (diulang hingga Dec 2025)
 * 
 * -- Expense: Biaya Sewa Bulanan (2 juta per bulan)
 * INSERT INTO transactions 
 * (user_account_id, financial_account_id, transaction_group_id, entry_type, amount, balance_effect, description, is_balance, created_at, updated_at)
 * VALUES
 * (1, 2, UUID(), 'debit', 2000000, 'decrease', 'Biaya Sewa Bulanan Jan 2025', 1, '2025-01-01', '2025-01-01'),
 * ... (diulang untuk 12 bulan)
 * 
 * 5. MAIN QUERY - SUM INCOME BY PERIOD
 * ================================================================
 * SELECT 
 *     DATE_FORMAT(t.created_at, '%Y-%m') AS periode,
 *     COALESCE(SUM(t.amount), 0) AS total_income
 * FROM transactions t
 * INNER JOIN financial_accounts fa 
 *     ON t.financial_account_id = fa.id
 * WHERE 
 *     t.user_account_id = 1                    -- Filter untuk user_account_id tertentu
 *     AND fa.type = 'IN'                       -- Hanya income accounts
 *     AND t.balance_effect = 'increase'        -- Hanya yang menambah balance
 *     AND fa.is_group = 0                      -- Hanya non-group accounts
 *     AND t.created_at BETWEEN '2025-01-01' AND '2025-12-31'
 * GROUP BY DATE_FORMAT(t.created_at, '%Y-%m')
 * ORDER BY periode ASC;
 * 
 * HASIL QUERY (Output):
 * +---------+--------------+
 * | periode | total_income |
 * +---------+--------------+
 * | 2025-01 |      8000000 |
 * | 2025-02 |      8000000 |
 * | 2025-03 |      8000000 |
 * | 2025-04 |      8000000 |
 * | 2025-05 |     13000000 |  ← Bonus tambahan 5 juta
 * | 2025-06 |      8000000 |
 * | ...     |         ...  |
 * | 2025-12 |      8000000 |
 * +---------+--------------+
 * 
 * PENJELASAN QUERY:
 * 1. DATE_FORMAT(t.created_at, '%Y-%m') → Format tanggal menjadi YYYY-MM (tahun-bulan)
 * 2. SUM(t.amount) → Jumlahkan amount transaksi
 * 3. INNER JOIN financial_accounts → Hubungkan untuk filter tipe akun
 * 4. WHERE fa.type = 'IN' → Hanya akun dengan tipe Income
 * 5. WHERE t.balance_effect = 'increase' → Hanya transaksi yang menambah balance
 * 6. GROUP BY DATE_FORMAT(...) → Kelompokkan hasil berdasarkan bulan
 * 7. ORDER BY periode ASC → Urutkan dari bulan tertua ke terbaru
 * ================================================================
 */

class ReportController extends Controller
{
    /**
     * Mengambil ringkasan total pendapatan (Income) per bulan dengan metadata lengkap
     */
    public function incomeSummary(Request $request)
    {
        // Ambil demo user (untuk testing)
        $user = User::where('email', 'demo_full@duweet.com')->first();

        if (!$user) {
            return response()->json(['error' => 'Demo user not found.'], 404);
        }

        $userAccount = DB::table('user_accounts')->where('id_user', $user->id)->first();

        if (!$userAccount) {
            return response()->json(['error' => 'User account configuration not found.'], 404);
        }
        $userAccountId = $userAccount->id;
        
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

        // Default periode
        $defaultStartDate = Carbon::create(2025, 1, 1)->startOfDay();
        $defaultEndDate = Carbon::create(2025, 12, 31)->endOfDay();

        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : $defaultStartDate;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $defaultEndDate;

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'], 400);
        }

        // ----------------------------------------------------
        // 3. LOGIKA QUERY DATABASE
        // ----------------------------------------------------
        try {
            // Use the same config key used by migrations (singular 'transaction') for consistency
            $transactionsTable = config('db_tables.transaction', 'transactions');
            $accountsTable = config('db_tables.financial_account', 'financial_accounts');

            $t = $transactionsTable; // alias for readability
            $a = $accountsTable;

            $summaryQuery = DB::table($t)
                ->join($a, "$t." . TransactionColumns::FINANCIAL_ACCOUNT_ID, '=', "$a.id")
                ->where("$t." . TransactionColumns::USER_ACCOUNT_ID, $userAccountId)
                ->where("$a.type", 'IN')
                ->where("$t." . TransactionColumns::BALANCE_EFFECT, 'increase')
                ->where("$a.is_group", false);

            // Pilih expression periode berdasarkan driver DB agar portable
            try {
                $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            } catch (\Exception $e) {
                $driver = 'mysql';
            }

            if ($driver === 'sqlite') {
                $periodeExpr = "strftime('%Y-%m', $t." . TransactionColumns::CREATED_AT . ")";
            } elseif ($driver === 'pgsql' || $driver === 'postgres') {
                $periodeExpr = "to_char($t." . TransactionColumns::CREATED_AT . ", 'YYYY-MM')";
            } else {
                $periodeExpr = "DATE_FORMAT($t." . TransactionColumns::CREATED_AT . ", '%Y-%m')";
            }

            $summary = $summaryQuery
                ->selectRaw("$periodeExpr as periode")
                ->selectRaw("COALESCE(SUM($t." . TransactionColumns::AMOUNT . "), 0) as total_income")
                ->whereBetween("$t." . TransactionColumns::CREATED_AT, [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
                ->groupBy(DB::raw($periodeExpr))
                ->orderBy('periode', 'asc')
                ->get();

            // Format response dengan metadata lengkap
            $response = [
                'user' => $userData,
                'user_account' => $userAccountData,
                'summary' => $summary,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query gagal dieksekusi.',
                'message' => $e->getMessage(),
                'hint' => 'Periksa nama tabel dan status koneksi database.'
            ], 500);
        }
    }
}
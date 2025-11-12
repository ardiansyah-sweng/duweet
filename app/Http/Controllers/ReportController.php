<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserAccount; // Asumsi Model ini ada atau dibuat
use App\Constants\TransactionColumns;
use Carbon\Carbon;

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
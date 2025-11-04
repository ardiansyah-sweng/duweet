<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon; // <-- 1. Kita butuh Carbon untuk mengelola tanggal

class ReportController extends Controller
{
    /**
     * Fungsi BARU untuk mengambil total income per periode,
     * berdasarkan tabel TRANSACTIONS (sesuai PRD).
     */
    public function getIncomeSumByPeriode(Request $request)
    {
        // $user = Auth::user(); // <-- NONAKTIFKAN SEMENTARA
        
        // ---- 3. KODE TESTING (PURA-PURA LOGIN) ----
        $user = User::where('email', 'demo@duweet.com')->first();
        // ------------------------------------------

        if (!$user) {
            return response()->json(['error' => 'Demo user "demo@duweet.com" not found. Run "php artisan migrate:fresh --seed"'], 404);
        }

        try {
            $userAccount = DB::table('user_accounts')->where('user_id', $user->id)->first();
            
            if (!$userAccount) {
                return response()->json(['error' => 'User account configuration not found for demo user.'], 404);
            }
            $userAccountId = $userAccount->id;

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to find user account: ' . $e->getMessage()], 500);
        }

        // ---- 2. DAPATKAN RENTANG TANGGAL DARI URL ----
        // Contoh: /.../income-summary?start_date=2025-10-01&end_date=2025-10-31
        try {
            // Beri nilai default: 30 hari terakhir jika user tidak menginput
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now();
        
        } catch (\Exception $e) {
            // Error jika format tanggal salah
            return response()->json(['error' => 'Format tanggal salah. Harap gunakan YYYY-MM-DD.'], 400);
        }


        $transactionsTable = config('db_tables.transactions', 'transactions'); 
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        try {
            $query = DB::table($transactionsTable)
                ->join($accountsTable, "$transactionsTable.financial_account_id", "=", "$accountsTable.id")
                ->select(
                    // PERBAIKAN DI SINI: Mengganti DATE_FORMAT dengan strftime untuk SQLite
                    DB::raw("strftime('%Y-%m', $transactionsTable.created_at) as periode"),
                    DB::raw("SUM($transactionsTable.amount) as total_income")
                )
                
                // ---- FILTER PENTING ----
                ->where("$transactionsTable.user_account_id", $userAccountId)
                ->where("$accountsTable.type", 'IN') 
                ->where("$transactionsTable.balance_effect", 'increase')
                ->where("$accountsTable.is_group", false)
                
                // ---- 3. TAMBAHKAN FILTER RENTANG TANGGAL ----
                // Catatan: Fungsi DATE() ini kompatibel dengan SQLite.
                ->whereBetween(DB::raw("DATE($transactionsTable.created_at)"), [$startDate->toDateString(), $endDate->toDateString()])
                
                // -------------------------
                
                ->groupBy('periode')
                ->orderBy('periode', 'desc');

            $summary = $query->get();

            return response()->json($summary);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
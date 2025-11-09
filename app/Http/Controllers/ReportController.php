<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserAccount; // Asumsi Model ini ada atau dibuat
use Carbon\Carbon; 

class ReportController extends Controller
{
    /**
     * Mengambil ringkasan total pendapatan (Income) per bulan
     */
    public function incomeSummary(Request $request) 
    {
        // ... (Logika User & Akun tetap sama)
        $user = User::where('email', 'demo_full@duweet.com')->first(); 
        
        if (!$user) {
            return response()->json(['error' => 'Demo user not found.'], 404);
        }

        $userAccount = DB::table('user_accounts')->where('id_user', $user->id)->first();
            
        if (!$userAccount) {
            return response()->json(['error' => 'User account configuration not found.'], 404);
        }
        $userAccountId = $userAccount->id; 
        
        // ... (Logika Tanggal tetap sama)
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
            // Gunakan string literal jika config tidak ada:
            $transactionsTable = config('db_tables.transactions', 'transactions'); 
            $accountsTable = config('db_tables.financial_account', 'financial_accounts');

            $summary = DB::table($transactionsTable)
                ->join($accountsTable, "$transactionsTable.financial_account_id", "=", "$accountsTable.id")
                ->select(
                    DB::raw("DATE_FORMAT($transactionsTable.created_at, '%Y-%m') as periode"),
                    DB::raw("SUM($transactionsTable.amount) as total_income")
                )
                // Filter berdasarkan Akun dan Tipe Transaksi
                ->where("$transactionsTable.user_account_id", $userAccountId)
                ->where("$accountsTable.type", 'IN') 
                ->where("$transactionsTable.balance_effect", 'increase')
                ->where("$accountsTable.is_group", false)
                
                // Filter Tanggal
                ->whereBetween("$transactionsTable.created_at", [$startDate, $endDate])
                
                // Grouping dan Ordering
                ->groupBy('periode')
                ->orderBy('periode', 'asc')
                ->get();

            return response()->json($summary);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query gagal dieksekusi.',
                'message' => $e->getMessage(),
                'hint' => 'Periksa nama tabel dan status koneksi database.'
            ], 500);
        }
    }
}
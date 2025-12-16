<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserAccount; // Asumsi Model ini ada atau dibuat
use App\Models\Transaction; // <-- PENTING: Import Model Transaction
use App\Constants\TransactionColumns;
use Carbon\Carbon;

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
     * Untuk admin: Ambil ringkasan total pengeluaran per periode (bulan).
     * Jika diberikan query param `user_account_id` maka akan mengembalikan untuk user tersebut,
     * jika tidak diberikan maka mengakumulasi untuk semua user (admin view).
     */
    public function expensesSummaryAdmin(Request $request)
    {
        // Penanganan Periode (sama seperti incomeSummary)
        $defaultStartDate = Carbon::create(2025, 1, 1)->startOfDay();
        $defaultEndDate = Carbon::create(2025, 12, 31)->endOfDay();

        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : $defaultStartDate;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $defaultEndDate;

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'], 400);
        }

        // optional user_account_id filter (untuk admin yang ingin melihat per user)
        $userAccountId = $request->query('user_account_id');
        $userAccountId = $userAccountId ? (int)$userAccountId : null;

        try {
            $summary = Transaction::getExpensesSummaryByPeriod(
                $userAccountId,
                $startDate,
                $endDate
            );

            return response()->json([
                'summary' => $summary,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil ringkasan pengeluaran.',
                'message' => $e->getMessage(),
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
}
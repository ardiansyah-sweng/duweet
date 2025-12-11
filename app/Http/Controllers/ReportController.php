<?php

namespace App\Http\Controllers;

use App\Models\UserFinancialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\Transaction;
use App\Constants\TransactionColumns;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    /**
     * GET /api/report/admin/liquid-assets-per-user
     * 
     * Query liquid asset per user untuk ADMIN
     * Menampilkan breakdown liquid assets per user
     * Sesuai PRD: type AS (Asset) yang is_active
     */
    public function adminLiquidAssetsPerUser()
    {
        try {
            $data = UserFinancialAccount::getLiquidAssetsPerUser();

            return response()->json([
                'status' => 'success',
                'message' => 'Liquid assets per user retrieved successfully',
                'data' => $data,
                'summary' => [
                    'total_users' => $data->count(),
                    'grand_total' => $data->sum('total_liquid_assets'),
                    'formatted_grand_total' => $this->rupiah($data->sum('total_liquid_assets')),
                ],
                'generated_at' => now()->toIso8601String(),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * GET /api/report/user/{id}/liquid-assets
     * 
     * Query liquid asset untuk USER SPESIFIK
     */
    public function userLiquidAsset(int $id)
    {
        try {
            $liquidTypes = ['AS', 'LI'];

            $row = DB::table('users as u')
                ->leftJoin('user_financial_accounts as ufa', 'ufa.user_id', '=', 'u.id')
                ->leftJoin('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
                ->where('u.id', $id)
                ->whereIn('fa.type', $liquidTypes)
                ->where(function ($q) {
                    $q->whereNull('ufa.is_active')->orWhere('ufa.is_active', 1);
                })
                ->groupBy('u.id', 'u.name')
                ->select([
                    'u.id',
                    'u.name',
                    DB::raw('COALESCE(SUM(ufa.balance),0) as total_liquid_asset'),
                ])
                ->first();

            if (!$row) {
                return response()->json([
                    'status'  => 'not_found',
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            $total = (int) $row->total_liquid_asset;

            return response()->json([
                'status'             => 'success',
                'user_id'            => (int) $row->id,
                'name'               => $row->name,
                'total_liquid_asset' => $total,
                'formatted'          => $this->rupiah($total),
                'generated_at'       => now()->toIso8601String(),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

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
}

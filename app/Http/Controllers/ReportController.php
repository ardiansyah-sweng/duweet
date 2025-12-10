<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserFinancialAccount;
use App\Models\UserAccount;
use App\Models\Transaction;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function usersWithoutAccounts()
    {
        $users = User::getAllUsersWithoutAccounts();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Tidak ada user yang belum memiliki akun finansial.',
                'total_users' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user yang belum memiliki akun finansial.',
            'total_users' => $users->count(),
            'data' => $users
        ], 200);
    }

    public function usersWithoutActiveAccounts()
    {
        $users = User::getAllUsersWithoutActiveAccounts();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Tidak ada user yang tidak memiliki akun aktif.',
                'total_users' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user yang tidak memiliki akun aktif.',
            'total_users' => $users->count(),
            'data' => $users
        ], 200);
    }

    public function userLiquidAsset($id)
    {
        $assets = UserFinancialAccount::where('user_account_id', $id)
            ->with(['financialAccount' => function ($query) {
                $query->select('id', 'name', 'type', 'balance');
            }])
            ->get(['id', 'user_account_id', 'financial_account_id', 'balance', 'is_active', 'created_at']);

        if ($assets->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'User tidak memiliki akun finansial atau data aset tidak ditemukan.',
                'total_assets' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar aset likuid milik user.',
            'user_account_id' => (int) $id,
            'total_assets' => $assets->count(),
            'data' => $assets
        ], 200);
    }

    public function incomeSummary(Request $request)
    {
        $baseData = $this->getReportBaseData($request);

        if ($baseData instanceof \Illuminate\Http\JsonResponse) {
            return $baseData;
        }

        ['user' => $user, 'userAccount' => $userAccount, 'userData' => $userData, 'userAccountData' => $userAccountData] = $baseData;
        $userAccountId = $userAccount->id;

        $defaultStartDate = Carbon::create(2025, 1, 1)->startOfDay();
        $defaultEndDate = Carbon::create(2025, 12, 31)->endOfDay();

        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : $defaultStartDate;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $defaultEndDate;

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'], 400);
        }

        try {
            $summary = Transaction::getIncomeSummaryByPeriod($userAccountId, $startDate, $endDate);

            return response()->json([
                'user' => $userData,
                'user_account' => $userAccountData,
                'summary' => $summary,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil ringkasan transaksi.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getReportBaseData(Request $request)
    {
        $email = $request->query('email');
        $userId = $request->query('user_id');
        $userAccountId = $request->query('user_account_id');

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

        if (!isset($userAccount) || $userAccount === null) {
            $userAccount = DB::table('user_accounts')->where('id_user', $user->id)->first();

            if (!$userAccount) {
                $userAccount = DB::table('user_accounts')->first();
                if (!$userAccount) {
                    return response()->json(['error' => 'No user accounts found in the system. Please seed the database first.'], 404);
                }
                $user = User::find($userAccount->id_user);
            }
        }

        if (!$userAccount) {
            return response()->json(['error' => 'User account configuration not found for user ID ' . $user->id . '.'], 404);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'provinsi' => $user->provinsi ?? null,
            'kabupaten' => $user->kabupaten ?? null,
        ];

        $userAccountData = [
            'id' => $userAccount->id,
            'username' => $userAccount->username ?? null,
            'email' => $userAccount->email ?? null,
        ];

        return compact('user', 'userAccount', 'userData', 'userAccountData');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Transaction;

// Import constants
use App\Constants\UserColumns;
use App\Constants\UserAccountColumns;
use App\Constants\UserTelephoneColumns;
use App\Constants\UserFinancialAccountColumns;
use App\Constants\TransactionColumns;

class UserController extends Controller
{
    /**
     * Index: Get users belum setup account
     */
    public function index(Request $request)
    {
        try {
            $users = User::userBelumSetupAccount();

            return response()->json([
                'status' => 'success',
                'keterangan' => 'Data user yang belum setup account',
                'total_data' => $users->count(),
                'data' => $users
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'keterangan' => 'Error: ' . $e->getMessage(),
                'total_data' => 0,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get users yang sudah setup account
     */
    public function sudahSetupAccount(Request $request)
    {
        try {
            $users = User::userSudahSetupAccount();

            return response()->json([
                'status' => 'success',
                'keterangan' => 'Data user yang sudah setup account',
                'total_data' => $users->count(),
                'data' => $users
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'keterangan' => 'Error: ' . $e->getMessage(),
                'total_data' => 0,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get all users dengan status setup account
     * Output: id, nama, email, setup_account (0/1), status_account (Belum Setup/Sudah Setup)
     */
    public function getAllWithStatus()
    {
        try {
            $users = User::getAllUsersWithAccountStatus();

            return response()->json([
                'status' => 'success',
                'keterangan' => 'Data user berdasarkan status setup account',
                'total_data' => $users->count(),
                'data' => $users
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'keterangan' => 'Error: ' . $e->getMessage(),
                'total_data' => 0,
                'data' => []
            ], 500);
        }
    }


    /**
     * Terima request, validasi, dan delegasikan insert ke model (createUserRaw).
     */
    public function createUserRaw(Request $request): JsonResponse
    {
        // Validasi input dasar di controller — biar model tetap bertanggung jawab atas query
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'provinsi' => 'sometimes|string|max:255',
            'kabupaten' => 'sometimes|string|max:255',
            'kecamatan' => 'sometimes|string|max:255',
            'jalan' => 'sometimes|string',
            'kode_pos' => 'sometimes|string|max:20',
            'tanggal_lahir' => 'sometimes|integer|min:1|max:31',
            'bulan_lahir' => 'sometimes|integer|min:1|max:12',
            'tahun_lahir' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'usia' => 'sometimes|integer|min:0',
            'telephones' => 'sometimes|array',
            'telephones.*' => 'string',
        ]);

        $result = User::createUserRaw($validated);

        if (is_string($result)) {
            return response()->json([
                'success' => false,
                'message' => $result
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
        ], 201);
    }

    public function destroy($id)
    {
        $user = DB::table('users')
            ->where(UserColumns::ID, $id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        DB::beginTransaction();

        try {
            /**
             * 1️⃣ Ambil semua user_account ID
             */
            $userAccountIds = DB::table('user_accounts')
                ->where(UserAccountColumns::ID_USER, $id)
                ->pluck(UserAccountColumns::ID);

            /**
             * 2️⃣ Hapus transaksi
             */
            if ($userAccountIds->isNotEmpty()) {
                Transaction::deleteByUserAccountIds($userAccountIds);
            }

            // 3️⃣ Hapus user_financial_accounts
            DB::table('user_financial_accounts')
                ->where(
                    UserFinancialAccountColumns::USER_ACCOUNT_ID,
                    $id
                )
                ->delete();

            // 4️⃣ Hapus user_telephones
            DB::table('user_telephones')
                ->where(
                    UserTelephoneColumns::USER_ID,
                    $id
                )
                ->delete();

            // 5️⃣ Hapus user_accounts
            DB::table('user_accounts')
                ->whereIn(
                    UserAccountColumns::ID,
                    $userAccountIds
                )
                ->delete();

            // 6️⃣ Hapus user utama
            DB::table('users')
                ->where(UserColumns::ID, $id)
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'User dan seluruh data terkait berhasil dihapus permanen.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}

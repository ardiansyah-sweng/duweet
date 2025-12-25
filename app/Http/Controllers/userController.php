<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction;
use App\Models\User;

// Import constants
use App\Constants\UserColumns;
use App\Constants\UserAccountColumns;
use App\Constants\UserTelephoneColumns;
use App\Constants\UserFinancialAccountColumns;
use App\Constants\TransactionColumns;

class UserController extends Controller
{
    /**
     * Index: Get users belum setup account dengan dynamic filtering
     * Query params: paginate, per_page, min_age, max_age, provinsi, limit, with_age_group
     * Contoh: /api/users/belum-setup?paginate=true&min_age=20&max_age=30&provinsi=Jawa Barat
     */
    public function index(Request $request)
    {
        try {
            $users = User::userBelumSetupAccount();
            
            return response()->json([
                'status' => 'success',
                'keterangan' => 'Data user yang belum setup account',
                'total_data' => count($users),
                'data' => $users
            ]);
        } catch (\Exception $e) {
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
                'total_data' => count($users),
                'data' => $users
            ]);
        } catch (\Exception $e) {
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
                'total_data' => count($users),
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'keterangan' => 'Error: ' . $e->getMessage(),
                'total_data' => 0,
                'data' => []
            ], 500);
        }
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
             * Controller tahu relasi via constant,
             * tapi tidak tahu detail DML transaksi
             */
            $userAccountIds = DB::table('user_accounts')
                ->where(UserAccountColumns::ID_USER, $id)
                ->pluck(UserAccountColumns::ID);

            /**
             * 2️⃣ Hapus transaksi
             * Kolom relasi tetap didefinisikan oleh TransactionColumns
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
                    UserTelephoneColumns::ID_USER,
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

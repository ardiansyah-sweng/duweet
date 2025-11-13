<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Import constants
use App\Constants\UserColumns;
use App\Constants\UserAccountColumns;
use App\Constants\UserTelephoneColumns;
use App\Constants\UserFinancialAccountColumns;
use App\Constants\TransactionColumns;

class UserController extends Controller
{
    /**
     * Menghapus user (hard delete) dan semua data terkait:
     * user_accounts, user_telephones, user_financial_accounts, dan transactions.
     */
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
            // 1️⃣ Ambil semua user_account milik user
            $userAccountIds = DB::table('user_accounts')
                ->where(UserAccountColumns::ID_USER, $id)
                ->pluck(UserAccountColumns::ID);

            // 2️⃣ Hapus semua transaksi yang terkait user_account
            if ($userAccountIds->isNotEmpty()) {
                DB::table('transactions')
                    ->whereIn(TransactionColumns::USER_ACCOUNT_ID, $userAccountIds)
                    ->delete();
            }

            // 3️⃣ Hapus semua user_financial_accounts milik user
            DB::table('user_financial_accounts')
                ->where(UserFinancialAccountColumns::USER_ID, $id)
                ->delete();

            // 4️⃣ Hapus semua nomor telepon milik user
            DB::table('user_telephones')
                ->where(UserTelephoneColumns::ID_USER, $id)
                ->delete();

            // 5️⃣ Hapus semua akun login milik user (user_accounts)
            if ($userAccountIds->isNotEmpty()) {
                DB::table('user_accounts')
                    ->whereIn(UserAccountColumns::ID, $userAccountIds)
                    ->delete();
            }

            // 6️⃣ Hapus user utama
            DB::table('users')
                ->where(UserColumns::ID, $id)
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'User dan semua data terkait (accounts, telephones, financials, transactions) berhasil dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

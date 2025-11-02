<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Menghapus user (hard delete) dan semua data terkait:
     * user_accounts, user_telephones, user_financial_accounts, dan transactions.
     */
    public function destroy($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        DB::beginTransaction();

        try {
            // 1️⃣ Ambil semua user_account milik user
            $userAccountIds = DB::table('user_accounts')
                ->where('user_id', $id)
                ->pluck('id');

            // 2️⃣ Hapus semua transaksi yang terkait user_account
            if ($userAccountIds->isNotEmpty()) {
                DB::table('transactions')
                    ->whereIn('user_account_id', $userAccountIds)
                    ->delete();
            }

            // 3️⃣ Hapus semua user_financial_accounts milik user
            DB::table('user_financial_accounts')
                ->where('user_id', $id)
                ->delete();

            // 4️⃣ Hapus semua nomor telepon milik user
            DB::table('user_telephones')
                ->where('user_id', $id)
                ->delete();

            // 5️⃣ Hapus semua akun login milik user (user_accounts)
            if ($userAccountIds->isNotEmpty()) {
                DB::table('user_accounts')
                    ->whereIn('id', $userAccountIds)
                    ->delete();
            }

            // 6️⃣ Hapus user utama
            DB::table('users')
                ->where('id', $id)
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

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
     * Menampilkan semua user
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json(['message' => 'OK', 'data' => $users]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail user berdasarkan ID
     */
    public function show($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }
            return response()->json(['message' => 'OK', 'data' => $user]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Pencarian user berdasarkan nama/email/alamat
     */
    public function search(Request $request)
    {
        try {
            $q = (string) $request->input('q', '');
            if ($q === '') {
                return response()->json(['message' => 'Parameter q wajib diisi', 'data' => []], 400);
            }

            $users = User::query()
                ->where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->orWhere('jalan', 'like', "%$q%")
                ->orWhere('kabupaten', 'like', "%$q%")
                ->orWhere('provinsi', 'like', "%$q%")
                ->get();

            return response()->json(['message' => 'OK', 'data' => $users]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
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
                Transaction::deleteByUserAccountIds(
                    $userAccountIds,
                    TransactionColumns::USER_ACCOUNT_ID
                );
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Transaction;

// Import constants
use App\Constants\UserColumns;
use App\Constants\UserAccountColumns;
use App\Constants\UserTelephoneColumns;
use App\Constants\UserFinancialAccountColumns;
use App\Constants\TransactionColumns;
use Carbon\Carbon;


class UserController extends Controller
{
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

    public function getUserAccounts($id): JsonResponse
    {
        
        $user = DB::table('users')
            ->where(UserColumns::ID, $id)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        $accounts = User::getUserAccounts($id);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data accounts.',
            'data' => [
                'user_id' => (int) $id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'accounts' => $accounts,
                'total_accounts' => count($accounts)
            ]
        ], 200);
    }

    public function AmbilDataUserYangLogin(Request $request)
    {
        try {
            
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan. Silakan login terlebih dahulu.'
                ], 401);
            }

            $authData = Cache::get('auth_token_' . $token);

            if (!$authData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah expired. Silakan login kembali.'
                ], 401);
            }

            $userData = User::AmbilDataUserYangLogin($authData['user_account_id']);

            if (!$userData) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan atau akun tidak aktif.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil.',
                'data' => $userData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(): JsonResponse
    {
        $users = User::GetUser();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data users.',
            'data' =>[
                'users' => $users,
                'total_users' => count($users)
            ]
        ], 200);
    }

    public function countUserpertanggalandbulan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'month' => 'nullable|date_format:Y-m',   
        ]);

        if (empty($validated['date']) && empty($validated['month'])) {
            return response()->json([
                'success' => false,
                'message' => 'Kirim salah satu: date (Y-m-d) atau month (Y-m).',
            ], 422);
        }

        if (!empty($validated['date']) && !empty($validated['month'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gunakan salah satu saja: date atau month.',
            ], 422);
        }

        $year = null;
        $month = null;
        $day = null;

        if (!empty($validated['date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $validated['date']);
            $year = (int) $date->year;
            $month = (int) $date->month;
            $day = (int) $date->day;
        } elseif (!empty($validated['month'])) {
            $monthOnly = Carbon::createFromFormat('Y-m', $validated['month']);
            $year = (int) $monthOnly->year;
            $month = (int) $monthOnly->month;
        }

        $data = User::countUserpertanggaldanbulan($year, $month, $day);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghitung user.',
            'data' => $data,
        ]);
    }

}

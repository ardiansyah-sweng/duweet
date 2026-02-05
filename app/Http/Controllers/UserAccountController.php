<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <--- WAJIB ADA karena dipakai di store()
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    /**
     * ============================
     * WEB LISTING
     * ============================
     */
    public function indexWeb()
    {
        $userAccounts = UserAccount::with('user')->get();
        $totalAccounts = $userAccounts->count();
        $activeAccounts = $userAccounts->where(UserAccountColumns::IS_ACTIVE, true)->count();
        $verifiedAccounts = $userAccounts->whereNotNull(UserAccountColumns::VERIFIED_AT)->count();

        return view('user-accounts.index', compact(
            'userAccounts',
            'totalAccounts',
            'activeAccounts',
            'verifiedAccounts'
        ));
    }

    /**
     * ============================
     * API LISTING
     * ============================
     */
    public function index()
    {
        $userAccounts = UserAccount::with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $userAccounts
        ]);
    }

    /**
     * ============================
     * SHOW SINGLE USER ACCOUNT
     * ============================
     */
    public function show($id)
    {
        $userAccount = UserAccount::with('user')->find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $userAccount
        ]);
    }

    /**
     * ============================
     * CREATE USER ACCOUNT (RAW)
     * ============================
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            UserAccountColumns::ID_USER => 'required|exists:users,id',
            UserAccountColumns::USERNAME => 'required|string|unique:user_accounts,' . UserAccountColumns::USERNAME . '|max:255',
            UserAccountColumns::EMAIL => 'required|email|unique:user_accounts,' . UserAccountColumns::EMAIL . '|max:255',
            UserAccountColumns::PASSWORD => 'required|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        // 2. Panggil raw insert via model (Password di-hash di dalam Model)
        $result = UserAccount::insertUserAccountRaw($validated);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat UserAccount'
            ], 500);
        }

        // 3. Ambil ID terakhir menggunakan PDO (Membutuhkan import DB)
        $lastId = DB::getPdo()->lastInsertId();
        $userAccount = UserAccount::find($lastId);

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dibuat',
            'data' => $userAccount
        ], 201);
    }

    /**
     * ============================
     * UPDATE USER ACCOUNT (RAW)
     * ============================
     */
    public function update(Request $request, $id)
    {
        // Cek dulu apakah user ada
        $existing = UserAccount::cariUserById($id);

        if (!$existing) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            UserAccountColumns::USERNAME => 'sometimes|string|unique:user_accounts,' . UserAccountColumns::USERNAME . ',' . $id . '|max:255',
            UserAccountColumns::EMAIL => 'sometimes|email|unique:user_accounts,' . UserAccountColumns::EMAIL . ',' . $id . '|max:255',
            UserAccountColumns::PASSWORD => 'sometimes|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        // Panggil raw update via model
        $affected = UserAccount::updateUserAccountRaw($id, $validated);

        // Ambil data terbaru untuk response
        $updatedUserAccount = UserAccount::cariUserById($id);

        // Jika affected 0, mungkin data sama, tapi tetap sukses
        return response()->json([
            'success' => true,
            'message' => $affected > 0 ? 'UserAccount berhasil diupdate' : 'Tidak ada perubahan data',
            'data' => $updatedUserAccount
        ]);
    }

    /**
     * ============================
     * DELETE (ELOQUENT)
     * ============================
     */
    public function destroy($id)
    {
        $userAccount = UserAccount::find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        $userAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dihapus'
        ]);
    }

    /**
     * ============================
     * DELETE WITH RAW QUERY
     * ============================
     */
    public function destroyRaw($id)
    {
        $result = UserAccount::deleteUserAccountRaw($id);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }

    /**
     * ============================
     * RESET PASSWORD (DML)
     * ============================
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $user = UserAccount::cariUserByEmail($data['email']);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $updated = UserAccount::resetPasswordByEmail($data['email'], $data['new_password']);

        return response()->json([
            'updated' => $updated,
            'email' => $user->email,
            'message' => 'Password reset successful'
        ]);
    }

    /**
     * ============================
     * FIND BY ID (DML)
     * ============================
     */
    public function findById($id): JsonResponse
    {
        $user = UserAccount::cariUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User account tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * ============================
     * COUNT PER USER
     * ============================
     */
    public function countAccountsPerUser($userId): JsonResponse
    {
        $summary = UserAccount::HitungTotalAccountperUser($userId);

        if (!$summary) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * ============================
     * LIST ACTIVE USERS 
     * ============================
     */
    public function listActive(Request $request): JsonResponse
    {
        // Method ini harus ada di Model UserAccount
        $results = UserAccount::query_list_user_account_aktif();

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * ============================
     * GET NESTED STRUCTURE 
     * ============================
     */
    public function GetstructureNested(): JsonResponse
    {
        try {
            // Method ini harus ada di Model UserAccount
            $nestedStructure = UserAccount::GetStructureNestedAccountUser();

            return response()->json([
                'success' => true,
                'message' => 'Nested user account structure retrieved success',
                'count' => count($nestedStructure),
                'data' => $nestedStructure
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving nested user account structure',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * ============================
     * GET NOT LOGGED IN 
     * ============================
     */
    public function notLoggedIn(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        // Method ini harus ada di Model UserAccount
        $data = UserAccount::query_user_tidak_login_dalam_periode_tanggal(
            $startDate,
            $endDate
        );

        return response()->json([
            'success' => true,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_found' => count($data),
            'data' => $data
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
     * Store a new user account (RAW QUERY - Via Model)
     * Controller sekarang hanya validasi dan memanggil Model.
     * ============================
     * CREATE USER ACCOUNT
     * ============================
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        // Menggunakan Constant agar nama field validasi sinkron dengan database
        $validated = $request->validate([
            UserAccountColumns::ID_USER => 'required|exists:users,id',
            UserAccountColumns::USERNAME => 'required|string|unique:user_accounts,' . UserAccountColumns::USERNAME . '|max:255',
            UserAccountColumns::EMAIL => 'required|email|unique:user_accounts,' . UserAccountColumns::EMAIL . '|max:255',
            UserAccountColumns::PASSWORD => 'required|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        $validated[UserAccountColumns::PASSWORD] = bcrypt($validated[UserAccountColumns::PASSWORD]);

        $userAccount = UserAccount::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dibuat',
            'data' => $userAccount
        ], 201);
    }

    /**
     * Update a user account (RAW QUERY VERSION)
     * Menggunakan method static updateUserAccountRaw dari Model
     * ============================
     * UPDATE USER ACCOUNT
     * ============================
     */
    public function update(Request $request, $id)
    {
        // 1. Cek apakah user ada (Menggunakan Raw/Static method agar konsisten)
        $existingUser = UserAccount::cariUserById($id);

        if (!$existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        // 2. Validasi Input
        // Menggunakan 'sometimes' agar user bisa update sebagian data saja
        $validated = $request->validate([
            UserAccountColumns::USERNAME => 'sometimes|string|max:255|unique:user_accounts,' . UserAccountColumns::USERNAME . ',' . $id,
            UserAccountColumns::EMAIL => 'sometimes|email|max:255|unique:user_accounts,' . UserAccountColumns::EMAIL . ',' . $id,
            UserAccountColumns::PASSWORD => 'sometimes|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        // 3. Proses Update via Model (RAW Query)
        // Password hashing sudah ditangani otomatis di dalam method updateUserAccountRaw
        try {
            $affectedRows = UserAccount::updateUserAccountRaw($id, $validated);

            // 4. Ambil data terbaru untuk response
            $updatedUser = UserAccount::cariUserById($id);

            return response()->json([
                'success' => true,
                'message' => $affectedRows > 0 ? 'UserAccount berhasil diupdate' : 'Tidak ada perubahan data',
                'data' => $updatedUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * ======================================================
     * RESET PASSWORD – (DML VERSION)
     * ======================================================
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
            'new_password' => $data['new_password'],
            'message' => 'Password reset successful'
        ]);
    }

    /**
     * ======================================================
     * FIND USER ACCOUNT BY ID – (DML VERSION)
     * ======================================================
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
}
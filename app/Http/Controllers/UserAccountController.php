<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    // =========================================================================
    // WEB METHODS
    // =========================================================================
    
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

    // =========================================================================
    // API METHODS
    // =========================================================================

    public function index()
    {
        $userAccounts = UserAccount::with('user')->get();
        return response()->json(['success' => true, 'data' => $userAccounts]);
    }

    public function show($id)
    {
        $userAccount = UserAccount::with('user')->find($id);

        if (!$userAccount) {
            return response()->json(['success' => false, 'message' => 'UserAccount tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $userAccount]);
    }

    public function findById($id): JsonResponse
    {
        // Menggunakan method raw dari Model
        $user = UserAccount::cariUserById($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User account tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            UserAccountColumns::ID_USER => 'required|exists:users,id',
            UserAccountColumns::USERNAME => 'required|string|unique:user_accounts,' . UserAccountColumns::USERNAME . '|max:255',
            UserAccountColumns::EMAIL => 'required|email|unique:user_accounts,' . UserAccountColumns::EMAIL . '|max:255',
            UserAccountColumns::PASSWORD => 'required|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        $validated[UserAccountColumns::PASSWORD] = bcrypt($validated[UserAccountColumns::PASSWORD]);

        // Menggunakan Eloquent create (sesuai kode asli tim)
        $userAccount = UserAccount::create($validated);
        
        return response()->json([
            'success' => true, 
            'message' => 'UserAccount berhasil dibuat', 
            'data' => $userAccount
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // 1. Cek User Exist (Pakai Raw Query biar konsisten)
        $existingUser = UserAccount::cariUserById($id);

        if (!$existingUser) {
            return response()->json(['success' => false, 'message' => 'UserAccount tidak ditemukan'], 404);
        }

        // 2. Validasi
        $validated = $request->validate([
            UserAccountColumns::USERNAME => 'sometimes|string|max:255|unique:user_accounts,' . UserAccountColumns::USERNAME . ',' . $id,
            UserAccountColumns::EMAIL => 'sometimes|email|max:255|unique:user_accounts,' . UserAccountColumns::EMAIL . ',' . $id,
            UserAccountColumns::PASSWORD => 'sometimes|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        // 3. Update Raw
        try {
            $affected = UserAccount::updateUserAccountRaw($id, $validated);

            if ($affected > 0) {
                $updatedData = UserAccount::cariUserById($id);
                return response()->json([
                    'success' => true, 
                    'message' => 'UserAccount berhasil diupdate', 
                    'data' => $updatedData
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Tidak ada data yang berubah', 
                'data' => $existingUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $userAccount = UserAccount::find($id);

        if (!$userAccount) {
            return response()->json(['success' => false, 'message' => 'UserAccount tidak ditemukan'], 404);
        }

        $userAccount->delete();
        return response()->json(['success' => true, 'message' => 'UserAccount berhasil dihapus']);
    }

    public function destroyRaw($id)
    {
        $result = UserAccount::deleteUserAccountRaw($id);
        return response()->json($result, $result['success'] ? 200 : 500);
    }

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

    public function countAccountsPerUser($userId): JsonResponse
    {
        $summary = UserAccount::HitungTotalAccountperUser($userId);

        if (!$summary) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $summary]);
    }
}
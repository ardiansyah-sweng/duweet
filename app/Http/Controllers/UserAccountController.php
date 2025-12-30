<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
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

    public function index()
    {
        $userAccounts = UserAccount::with('user')->get();
        return response()->json([
            'success' => true,
            'data' => $userAccounts
        ]);
    }

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
        $userAccount = UserAccount::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dibuat',
            'data' => $userAccount
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userAccount = UserAccount::find($id);
        if (!$userAccount) {
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
        if (isset($validated[UserAccountColumns::PASSWORD])) {
            $validated[UserAccountColumns::PASSWORD] = bcrypt($validated[UserAccountColumns::PASSWORD]);
        }
        $userAccount->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil diupdate',
            'data' => $userAccount
        ]);
    }

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

    public function destroyRaw($id)
    {
        $result = UserAccount::deleteUserAccountRaw($id);
        if (!$result['success']) {
            return response()->json($result, 500);
        }
        return response()->json($result);
    }

    public function inactiveByPeriod(Request $request)
    {
        $days = $request->query('hari', 7);
        $data = UserAccount::query_user_yang_tidak_login_dalam_periode_tertentu($days);
        return response()->json([
            'success' => true,
            'days_threshold' => $days,
            'data' => $data
        ]);
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
            'new_password' => $data['new_password'],
            'message' => 'Password reset successful'
        ]);
    }

    /**
     * FUNGSI FIND BY EMAIL & ID (SEMUA VERSI DIPERTAHANKAN)
     */
    public function findByEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $user = UserAccount::cariUserByEmail($request->email);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function findById($id): JsonResponse
    {
        $user = UserAccount::cariUserById($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User account tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }
}
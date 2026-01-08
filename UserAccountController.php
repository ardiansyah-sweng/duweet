<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    /**
     * ============================
     * API LISTING USER ACCOUNT
     * ============================
     */
    public function index(): JsonResponse
    {
        $userAccounts = UserAccount::with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $userAccounts
        ]);
    }

    /**
     * ============================
     * SHOW USER ACCOUNT BY ID
     * ============================
     */
    public function show($id): JsonResponse
    {
        $userAccount = UserAccount::cariUserById($id);

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
     * CREATE USER ACCOUNT
     * ============================
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            UserAccountColumns::ID_USER => 'required|exists:users,id',
            UserAccountColumns::USERNAME => 'required|string|unique:user_accounts,username',
            UserAccountColumns::EMAIL => 'required|email|unique:user_accounts,email',
            UserAccountColumns::PASSWORD => 'required|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        $userAccount = UserAccount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dibuat',
            'data' => $userAccount
        ], 201);
    }

    /**
     * ============================
     * UPDATE USER ACCOUNT
     * ============================
     */
    public function update(Request $request, $id): JsonResponse
    {
        $userAccount = UserAccount::cariUserById($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            UserAccountColumns::USERNAME => 'sometimes|string|unique:user_accounts,username,' . $id,
            UserAccountColumns::EMAIL => 'sometimes|email|unique:user_accounts,email,' . $id,
            UserAccountColumns::PASSWORD => 'sometimes|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            $userAccount->{$key} = $value;
        }

        $userAccount->save();

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil diupdate',
            'data' => $userAccount
        ]);
    }

    /**
     * ============================
     * DELETE (ELOQUENT)
     * ============================
     */
    public function destroy($id): JsonResponse
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
     * DELETE RAW QUERY
     * ============================
     */
    public function destroyRaw($id): JsonResponse
    {
        $result = UserAccount::deleteUserAccountRaw($id);
        return response()->json($result);
    }

    /**
     * ============================
     * RESET PASSWORD (RAW)
     * ============================
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|string|min:6'
        ]);

        $user = UserAccount::cariUserByEmail($data['email']);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        UserAccount::resetPasswordByEmail($data['email'], $data['new_password']);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful'
        ]);
    }

    /**
     * ============================
     * FIND USER BY ID
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
     * LOGIN USER
     * ============================
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        $login = $request->login;
        $password = $request->password;

        $userAccount = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? UserAccount::cariUserByEmail($login, $password)
            : UserAccount::findByUsername($login, $password);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if (!$userAccount->{UserAccountColumns::IS_ACTIVE}) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => $userAccount
        ]);
    }
}

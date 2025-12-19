<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Masih dibutuhkan untuk method update

class UserAccountController extends Controller
{
    /**
     * Display a listing of user accounts (Web View)
     */
    public function indexWeb()
    {
        $userAccounts = UserAccount::with('user')->get();
        $totalAccounts = $userAccounts->count();
        $activeAccounts = $userAccounts->where(UserAccountColumns::IS_ACTIVE, true)->count();
        $verifiedAccounts = $userAccounts->whereNotNull(UserAccountColumns::VERIFIED_AT)->count();

        return view('user-accounts.index', compact('userAccounts', 'totalAccounts', 'activeAccounts', 'verifiedAccounts'));
    }

    /**
     * Display a listing of user accounts (API)
     */
    public function index()
    {
        $userAccounts = UserAccount::with('user')->get();

        return response()->json([
            'success' => true,
            'data'    => $userAccounts,
        ]);
    }

    /**
     * Display a specific user account
     */
    public function show($id)
    {
        $userAccount = UserAccount::with('user')->find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $userAccount,
        ]);
    }

    /**
     * Store a new user account (RAW QUERY - Via Model)
     * Controller sekarang hanya validasi dan memanggil Model.
     */
    public function storeRaw(Request $request)
    {
        // 1. Validasi Input
        // Menggunakan Constant agar nama field validasi sinkron dengan database
        $validated = $request->validate([
            UserAccountColumns::ID_USER   => 'required|exists:users,id',
            UserAccountColumns::USERNAME  => 'required|string|unique:user_accounts,' . UserAccountColumns::USERNAME,
            UserAccountColumns::EMAIL     => 'required|email|unique:user_accounts,' . UserAccountColumns::EMAIL,
            UserAccountColumns::PASSWORD  => 'required|string|min:8',
        ]);

        try {
            // 2. Panggil fungsi static di Model untuk eksekusi INSERT
            UserAccount::insertUserAccountRaw($validated);

            return response()->json([
                'success' => true,
                'message' => 'UserAccount berhasil ditambahkan',
                'data'    => [
                    UserAccountColumns::USERNAME => $validated[UserAccountColumns::USERNAME],
                    UserAccountColumns::EMAIL    => $validated[UserAccountColumns::EMAIL],
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal insert data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user account (Standard Eloquent)
     */
    public function update(Request $request, $id)
    {
        $userAccount = UserAccount::find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            UserAccountColumns::USERNAME => 'sometimes|string|unique:user_accounts,' . UserAccountColumns::USERNAME . ',' . $id . '|max:255',
            UserAccountColumns::EMAIL    => 'sometimes|email|unique:user_accounts,' . UserAccountColumns::EMAIL . ',' . $id . '|max:255',
            UserAccountColumns::PASSWORD => 'sometimes|string|min:8',
            UserAccountColumns::IS_ACTIVE => 'boolean',
        ]);

        if (isset($validated[UserAccountColumns::PASSWORD])) {
            $validated[UserAccountColumns::PASSWORD] = Hash::make($validated[UserAccountColumns::PASSWORD]);
        }

        $userAccount->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil diupdate',
            'data'    => $userAccount,
        ]);
    }

    /**
     * Delete a user account using Eloquent
     */
    public function destroy($id)
    {
        $userAccount = UserAccount::find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan',
            ], 404);
        }

        $userAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dihapus',
        ]);
    }

    /**
     * Delete a user account using raw query
     */
    public function destroyRaw($id)
    {
        $result = UserAccount::deleteUserAccountRaw($id);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }
}
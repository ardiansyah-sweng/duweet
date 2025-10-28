<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
    /**
     * Display a listing of user accounts (Web View)
     */
    public function indexWeb()
    {
        $userAccounts = UserAccount::with('user')->get();
        $totalAccounts = $userAccounts->count();
        $activeAccounts = $userAccounts->where('is_active', true)->count();
        $verifiedAccounts = $userAccounts->whereNotNull('email_verified_at')->count();

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
            'data' => $userAccounts
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
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $userAccount
        ]);
    }

    /**
     * Store a new user account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'username' => 'required|string|unique:user_accounts,username|max:255',
            'email' => 'required|email|unique:user_accounts,email|max:255',
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $userAccount = UserAccount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil dibuat',
            'data' => $userAccount
        ], 201);
    }

    /**
     * Update a user account
     */
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
            'username' => 'sometimes|string|unique:user_accounts,username,' . $id . '|max:255',
            'email' => 'sometimes|email|unique:user_accounts,email,' . $id . '|max:255',
            'password' => 'sometimes|string|min:8',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $userAccount->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'UserAccount berhasil diupdate',
            'data' => $userAccount
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

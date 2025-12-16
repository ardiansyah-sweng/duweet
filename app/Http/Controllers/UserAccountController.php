<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
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
     * ==============================
     * TUGAS UTAMA
     * Query ambil semua financial account milik user
     * ==============================
     */
    public function getAllAccounts($id)
    {
        // Cek user account
        $userAccount = UserAccount::find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

        // Query JOIN sesuai struktur database
        $accounts = DB::table('user_financial_account as ufa')
            ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
            ->where('ufa.user_account_id', $id)
            ->select(
                'fa.id',
                'fa.name',
                'fa.type',
                'ufa.initial_balance',
                'ufa.balance',
                'ufa.is_active'
            )
            ->get();

        return response()->json([
            'success' => true,
            'user_account_id' => $id,
            'total_accounts' => $accounts->count(),
            'accounts' => $accounts
        ]);
    }
}

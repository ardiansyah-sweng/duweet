<?php

namespace App\Http\Controllers;

use App\Constants\UserAccountColumns;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    
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

    public function getAllAccounts($id)
    {
        $userAccount = UserAccount::find($id);

        if (!$userAccount) {
            return response()->json([
                'success' => false,
                'message' => 'UserAccount tidak ditemukan'
            ], 404);
        }

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

    public function getAllAccountsByUser($userId)
    {
        // Ambil semua UserAccount milik user
        $userAccountIds = UserAccount::where('id_user', $userId)->pluck('id');

        if ($userAccountIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki UserAccount'
            ], 404);
        }

        // Ambil semua financial account milik user (via UserAccount)
        $accounts = DB::table('user_financial_account as ufa')
            ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
            ->whereIn('ufa.user_account_id', $userAccountIds)
            ->select(
                'fa.id as financial_account_id',
                'fa.name',
                'fa.type',
                'ufa.initial_balance',
                'ufa.balance',
                'ufa.is_active'
            )
            ->get();

        return response()->json([
            'success' => true,
            'user_id' => $userId,
            'total_accounts' => $accounts->count(),
            'accounts' => $accounts
        ]);
    }
}

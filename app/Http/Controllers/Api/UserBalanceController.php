<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constants\UserAccountColumns;

class UserBalanceController extends Controller
{
    /**
     * Return grouped balances by account type for the given user.
     *
     * Response shape:
     * [
     *   { "account_type": "AS", "total_balance": 12345 },
     *   { "account_type": "LI", "total_balance": -5000 },
     * ]
     */
    public function index(User $user): JsonResponse
    {
        // Allow resolving by user_account id (login account) if provided via query param
        // This helps when seeders populate user_accounts (login) instead of users.
        $userId = $user->id;
        $userAccountId = request()->query('user_account_id');
        if ($userAccountId) {
            $uaTable = config('db_tables.user_account', 'user_accounts');
            $resolved = DB::table($uaTable)->where('id', $userAccountId)->value('id_user');
            if ($resolved) {
                $userId = $resolved;
            }
        }

        // Resolve table name for financial accounts (with fallbacks)
        $tableAccounts = config('db_tables.financial_account') ?: (config('db_tables.account') ?: 'financial_accounts');

        $rows = DB::table('user_financial_accounts as ufa')
            ->join($tableAccounts . ' as a', 'ufa.financial_account_id', '=', 'a.id')
            ->select('a.type as account_type', DB::raw('SUM(ufa.balance) as total_balance'))
            ->where('ufa.user_id', $userId)
            ->when(request()->filled('account_type'), function ($q) {
                $q->where('a.type', request()->input('account_type'));
            })
            ->groupBy('a.type')
            ->get();

        return response()->json($rows);
    }

    /**
     * Resolve user from query parameters and return grouped balances.
     * Accepts: user_id, user_account_id, email
     * Example: GET /api/users/balances?user_account_id=12
     */
    public function byQuery(Request $request): JsonResponse
    {
        $userId = null;

        // 1) explicit user_id
        if ($request->filled('user_id')) {
            $userId = (int) $request->input('user_id');
        }

        // 2) resolve by user_account_id
        if (! $userId && $request->filled('user_account_id')) {
            $uaTable = config('db_tables.user_account', 'user_accounts');
            $resolved = DB::table($uaTable)->where('id', $request->input('user_account_id'))->value('id_user');
            if ($resolved) {
                $userId = $resolved;
            }
        }

        // 2b) resolve by username on user_accounts (login account)
        if (! $userId && $request->filled('username')) {
            $uaTable = config('db_tables.user_account', 'user_accounts');
            $uaUsernameCol = UserAccountColumns::USERNAME;
            $uaUserIdCol = UserAccountColumns::ID_USER;
            $resolved = DB::table($uaTable)->where($uaUsernameCol, $request->input('username'))->value($uaUserIdCol);
            if ($resolved) {
                $userId = $resolved;
            }
        }

        // 2c) resolve by account email on user_accounts (login email)
        if (! $userId && $request->filled('account_email')) {
            $uaTable = config('db_tables.user_account', 'user_accounts');
            $uaEmailCol = UserAccountColumns::EMAIL;
            $uaUserIdCol = UserAccountColumns::ID_USER;
            $resolved = DB::table($uaTable)->where($uaEmailCol, $request->input('account_email'))->value($uaUserIdCol);
            if ($resolved) {
                $userId = $resolved;
            }
        }
        // 3) resolve by email
        if (! $userId && $request->filled('email')) {
            $resolved = DB::table('users')->where('email', $request->input('email'))->value('id');
            if ($resolved) {
                $userId = $resolved;
            }
        }

        if (! $userId) {
            return response()->json([
                'message' => 'user not specified or could not be resolved. Provide user_id, user_account_id or email.'
            ], 400);
        }

        $tableAccounts = config('db_tables.financial_account') ?: (config('db_tables.account') ?: 'financial_accounts');

        $rows = DB::table('user_financial_accounts as ufa')
            ->join($tableAccounts . ' as a', 'ufa.financial_account_id', '=', 'a.id')
            ->select('a.type as account_type', DB::raw('SUM(ufa.balance) as total_balance'))
            ->where('ufa.user_id', $userId)
            ->when($request->filled('account_type'), function ($q) use ($request) {
                $q->where('a.type', $request->input('account_type'));
            })
            ->groupBy('a.type')
            ->get();

        return response()->json($rows);
    }
}

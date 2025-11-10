<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

class AccountController extends Controller
{
    public function totalPerUser()
    {
        $results = DB::select(<<<'SQL'
SELECT 
    u.id AS user_id,
    u.name AS user_name,
    SUM(uat.total_balance) AS total_balance
FROM user_account_totals AS uat
JOIN users AS u ON uat.user_id = u.id
JOIN accounts AS a ON uat.account_id = a.id
GROUP BY u.id, u.name
ORDER BY total_balance DESC
SQL
        );

        // Kirim hasil ke tampilan (view)
        return view('totals', ['results' => $results]);
    }

    /**
     * API: total balance per user (uses application's configured DB connection)
     */
    public function apiTotalPerUser(Request $request)
    {
        // Use Eloquent with relationship sum to leverage models
        $users = User::withSum('accountTotals as total_balance', 'total_balance')
            ->orderByDesc('total_balance')
            ->get(['id', 'name']);

        // Map to consistent response shape
        $results = $users->map(function ($u) {
            return [
                'user_id' => $u->id,
                'user_name' => $u->name,
                'total_balance' => number_format((float) ($u->total_balance ?? 0), 2, '.', ''),
            ];
        });

    return response()->json($results->values()->all());
    }

    /**
     * API: count of accounts per user (all users)
     */
    public function apiAccountCountPerUser()
    {
        $users = User::withCount('accountTotals')->orderByDesc('account_totals_count')->get(['id', 'name']);

        $results = $users->map(function ($u) {
            return [
                'user_id' => $u->id,
                'user_name' => $u->name,
                'account_count' => (int) ($u->account_totals_count ?? 0),
            ];
        });

    return response()->json($results->values()->all());
    }

    /**
     * API: count of accounts for a specific user
     */
    public function apiAccountCountForUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $count = DB::table('user_account_totals')
            ->where('user_id', $id)
            ->count('account_id');

        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'account_count' => $count,
        ]);
    }

    /**
     * API: list accounts for a specific user with per-account total_balance
     */
    public function apiAccountsForUser($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $accounts = \App\Models\Account::query()
            ->join('user_account_totals', 'user_account_totals.account_id', '=', 'accounts.id')
            ->where('user_account_totals.user_id', $id)
            ->select('accounts.*', 'user_account_totals.total_balance')
            ->get();

        return response()->json($accounts);
    }

    /**
     * API: totals per user with account counts
     * Returns: [{ user_id, user_name, total_balance (float), account_count (int) }, ...]
     */
    public function apiTotalsWithCounts(Request $request)
    {
        $users = User::withSum('accountTotals as total_balance', 'total_balance')
            ->withCount('accountTotals')
            ->orderByDesc('total_balance')
            ->get(['id', 'name']);

        $results = $users->map(function ($u) {
            return [
                'user_id' => $u->id,
                'user_name' => $u->name,
                // return numeric types: float for totals, int for counts
                'total_balance' => (float) ($u->total_balance ?? 0),
                'account_count' => (int) ($u->account_totals_count ?? 0),
            ];
        });

        return response()->json($results->values()->all());
    }
}

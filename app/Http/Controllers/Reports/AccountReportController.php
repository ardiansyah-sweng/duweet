<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountReportController extends Controller
{
    /**
     * Return counts of accounts per user.
     * - user_account_count: count of rows in `user_accounts` per user
     * - financial_account_count: count of active rows in `user_financial_accounts` per user
     *
     * Optional query param: `user_id` to filter to a specific user.
     */
    public function index(Request $request)
    {
        $filterUserId = $request->query('user_id');

        $bindings = [];
        $userWhere = '';
        if ($filterUserId !== null) {
            $userWhere = ' WHERE u.id = ? ';
            $bindings[] = $filterUserId;
        }

        $userAccountsSql = "
            SELECT u.id AS user_id, u.name, COUNT(ua.id) AS user_account_count
            FROM users u
            LEFT JOIN user_accounts ua ON ua.id_user = u.id
            " . $userWhere . "
            GROUP BY u.id, u.name
            ORDER BY user_account_count DESC
        ";

        $financialAccountsSql = "
            SELECT u.id AS user_id, u.name, COUNT(ufa.id) AS financial_account_count
            FROM users u
            LEFT JOIN user_financial_accounts ufa ON ufa.user_id = u.id AND ufa.is_active = 1
            " . $userWhere . "
            GROUP BY u.id, u.name
            ORDER BY financial_account_count DESC
        ";

        $userAccounts = DB::select($userAccountsSql, $bindings);
        $financialAccounts = DB::select($financialAccountsSql, $bindings);

        $result = [];

        foreach ($userAccounts as $r) {
            $result[$r->user_id] = [
                'user_id' => (int) $r->user_id,
                'name' => $r->name,
                'user_account_count' => (int) $r->user_account_count,
                'financial_account_count' => 0,
            ];
        }

        foreach ($financialAccounts as $r) {
            $uid = $r->user_id;
            if (!isset($result[$uid])) {
                $result[$uid] = [
                    'user_id' => (int) $r->user_id,
                    'name' => $r->name,
                    'user_account_count' => 0,
                    'financial_account_count' => (int) $r->financial_account_count,
                ];
            } else {
                $result[$uid]['financial_account_count'] = (int) $r->financial_account_count;
            }
        }

        return response()->json(array_values($result));
    }
}

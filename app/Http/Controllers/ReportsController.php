<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ReportsController extends Controller
{
    /**
     * Return total transactions per user as JSON.
     *
     * GET /api/reports/transactions-per-user
     */
    public function transactionsPerUser(Request $request)
    {
        // validate optional parameters
        $validator = Validator::make($request->all(), [
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'user_id' => 'nullable|integer|exists:users,id',
            'group_by_account_type' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $from = $request->query('from');
        $to = $request->query('to');
        $userId = $request->query('user_id');
        $groupByAccountType = $request->boolean('group_by_account_type');

        if (($from && !$to) || (!$from && $to)) {
            return response()->json(['status' => 'error', 'message' => 'Both from and to are required when filtering by date'], 422);
        }

        // Delegate to the model-level query
        $data = User::transactionTotals($from, $to, $userId, $groupByAccountType);

        return response()->json([
            'status' => 'success',
            'filters' => [
                'date_range' => $from && $to ? ['from' => $from, 'to' => $to] : null,
                'user_id' => $userId,
                'grouped_by_account_type' => $groupByAccountType,
            ],
            'data' => $data,
        ]);
    }
}

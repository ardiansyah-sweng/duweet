<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;

class ReportsController extends Controller
{
    /**
     * Return total transactions per user account as JSON.
     *
     * GET /api/reports/transactions-per-user-account
     * Query parameters:
     * - user_account_id: Filter by specific user account (optional)
     * 
     * Returns:
     * - user_account_id: User account ID
     * - user_account_email: User account email
     * - transaction_count: Count of unique transaction groups (COUNT DISTINCT transaction_group_id)
     */
    public function getTotalTransactionsPerUserAccount(Request $request)
    {
        // Validate optional parameter
        $validator = Validator::make($request->all(), [
            'user_account_id' => 'nullable|integer|exists:user_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $userAccountId = $request->query('user_account_id');

        // Get transaction totals per user account from model
        $data = Transaction::getTotalTransactionsPerUserAccount($userAccountId);

        return response()->json([
            'status' => 'success',
            'filter' => [
                'user_account_id' => $userAccountId,
            ],
            'count' => $data->count(),
            'data' => $data,
        ]);
    }
}


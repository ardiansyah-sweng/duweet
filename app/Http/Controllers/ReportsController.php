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
     * Return total transactions per user as JSON - simplified version.
     *
     * GET /api/reports/transactions-per-user
     * Query parameters:
     * - user_id: Filter by specific user (optional)
     */
    public function getTotalTransactionsPerUser(Request $request)
    {
        // Validate optional parameter
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->query('user_id');

        // Get transaction totals from model
        $data = User::getTotalTransactionsPerUser($userId);

        return response()->json([
            'status' => 'success',
            'filter' => [
                'user_id' => $userId,
            ],
            'count' => $data->count(),
            'data' => $data,
        ]);
    }
}

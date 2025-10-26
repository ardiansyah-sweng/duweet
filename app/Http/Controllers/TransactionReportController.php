<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class TransactionReportController extends Controller
{
    /**
     * Get total transactions per user with various filter options.
     * 
     * GET /api/reports/transactions/per-user
     * Query parameters:
     *   - user_id (optional) - filter by specific user ID
     *   - group_by (optional) - 'account-type' to group by account type, default is detailed breakdown
     * 
     * Default behavior: returns detailed breakdown (debit/credit)
     * If group_by=account-type: groups results by account type
     * 
     * @return JsonResponse
     */
    public function getTotalTransactionPerUser(): JsonResponse
    {
        try {
            $userId = request('user_id');
            $groupBy = request('group_by');
            
            $userIds = $userId ? [$userId] : null;
            
            // Get data with optional grouping
            $data = User::getTotalTransactionStats($userIds, $groupBy);
            
            // Determine message based on parameters
            if ($groupBy === 'account-type') {
                $message = 'Total transaksi per user berdasarkan tipe akun berhasil diambil';
            } else {
                $message = 'Detail total transaksi per user (debit/credit breakdown) berhasil diambil';
            }
            
            $response = [
                'success' => true,
                'message' => $message,
                'data' => $data,
                'total_records' => $data->count(),
            ];
            
            // Add filter info if applied
            $filters = [];
            if ($userId) $filters['user_id'] = $userId;
            if ($groupBy) $filters['group_by'] = $groupBy;
            
            if (!empty($filters)) {
                $response['filters'] = $filters;
            }
            
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }
}



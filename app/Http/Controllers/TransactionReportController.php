<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class TransactionReportController extends Controller
{
    /**
     * Get total transactions per user.
     * 
     * GET /api/reports/transactions/per-user
     * 
     * @return JsonResponse
     */
    public function getTotalTransactionPerUser(): JsonResponse
    {
        try {
            $data = User::getTotalTransactionStats();
            
            return response()->json([
                'success' => true,
                'message' => 'Total transaksi per user berhasil diambil',
                'data' => $data,
                'total_records' => $data->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get total transactions per user with detailed breakdown.
     * 
     * GET /api/reports/transactions/per-user/detailed
     * 
     * @return JsonResponse
     */
    public function getTotalTransactionPerUserDetailed(): JsonResponse
    {
        try {
            $data = User::getTotalTransactionStatsDetailed();
            
            return response()->json([
                'success' => true,
                'message' => 'Detail total transaksi per user berhasil diambil',
                'data' => $data,
                'total_records' => $data->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get total transactions per user grouped by account type.
     * 
     * GET /api/reports/transactions/per-user/by-account-type
     * 
     * @return JsonResponse
     */
    public function getTotalTransactionPerUserByAccountType(): JsonResponse
    {
        try {
            $data = User::getTotalTransactionByAccountType();
            
            return response()->json([
                'success' => true,
                'message' => 'Total transaksi per user berdasarkan tipe akun berhasil diambil',
                'data' => $data,
                'total_records' => $data->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get total transactions per user by date range.
     * 
     * GET /api/reports/transactions/per-user/by-date-range
     * Query parameters: start_date, end_date (Y-m-d format)
     * 
     * @return JsonResponse
     */
    public function getTotalTransactionPerUserByDateRange(): JsonResponse
    {
        try {
            $startDate = request('start_date');
            $endDate = request('end_date');
            
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter start_date dan end_date diperlukan',
                ], 400);
            }

            $data = User::getTotalTransactionByDateRange($startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'message' => 'Total transaksi per user dalam periode berhasil diambil',
                'data' => $data,
                'total_records' => $data->count(),
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }
}

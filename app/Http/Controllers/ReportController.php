<?php

namespace App\Http\Controllers;

use App\Models\UserFinancialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    /**
     * GET /api/report/admin/liquid-assets-per-user
     * 
     * Query liquid asset per user untuk ADMIN
     * Menampilkan breakdown liquid assets per user
     * Sesuai PRD: type AS (Asset) yang is_active
     */
    public function adminLiquidAssetsPerUser()
    {
        try {
            $data = UserFinancialAccount::getLiquidAssetsPerUser();

            return response()->json([
                'status' => 'success',
                'message' => 'Liquid assets per user retrieved successfully',
                'data' => $data,
                'summary' => [
                    'total_users' => $data->count(),
                    'grand_total' => $data->sum('total_liquid_assets'),
                    'formatted_grand_total' => $this->rupiah($data->sum('total_liquid_assets')),
                ],
                'generated_at' => now()->toIso8601String(),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * GET /api/report/user/{id}/liquid-assets
     * 
     * Query liquid asset untuk USER SPESIFIK
     */
    public function userLiquidAsset(int $id)
    {
        try {
            $liquidTypes = ['AS', 'LI'];

            $row = DB::table('users as u')
                ->leftJoin('user_financial_accounts as ufa', 'ufa.user_id', '=', 'u.id')
                ->leftJoin('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
                ->where('u.id', $id)
                ->whereIn('fa.type', $liquidTypes)
                ->where(function ($q) {
                    $q->whereNull('ufa.is_active')->orWhere('ufa.is_active', 1);
                })
                ->groupBy('u.id', 'u.name')
                ->select([
                    'u.id',
                    'u.name',
                    DB::raw('COALESCE(SUM(ufa.balance),0) as total_liquid_asset'),
                ])
                ->first();

            if (!$row) {
                return response()->json([
                    'status'  => 'not_found',
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            $total = (int) $row->total_liquid_asset;

            return response()->json([
                'status'             => 'success',
                'user_id'            => (int) $row->id,
                'name'               => $row->name,
                'total_liquid_asset' => $total,
                'formatted'          => $this->rupiah($total),
                'generated_at'       => now()->toIso8601String(),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }
}

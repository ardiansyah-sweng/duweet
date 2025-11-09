<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    public function userLiquidAsset(int $id)
    {
        try {
            $row = DB::table('users as u')
                ->leftJoin('user_financial_accounts as ufa', 'ufa.user_id', '=', 'u.id')
                ->leftJoin('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
                ->where('u.id', $id)
                // Hanya tipe Asset yang dihitung sebagai liquid asset
                ->where('fa.type', 'AS')
                // Hanya leaf account (non-group)
                ->where('fa.is_group', false)
                // Hanya relasi yang aktif
                ->where('ufa.is_active', 1)
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

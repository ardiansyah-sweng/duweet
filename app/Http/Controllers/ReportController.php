<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;


use App\Constants\UserColumns;
use App\Constants\AccountColumns;
use App\Constants\FinancialAccountColumns;
use App\Constants\UserFinancialAccountColumns;

class ReportController extends Controller
{
    private function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    public function userLiquidAsset(int $id)
    {
        try {
            // Validasi query parameters (optional)
            $validated = request()->validate([
                'type'             => 'nullable|string|in:AS,LI,AS+LI',  // Filter by account type
                'include_inactive' => 'nullable|boolean',                 // Include inactive accounts
                'min_balance'      => 'nullable|numeric',                 // Minimum balance filter
                'format'           => 'nullable|string|in:json,formatted' // Response format
            ]);

            // Find user using Eloquent
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'status'  => 'not_found',
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            // Prepare filter options for model method
            $options = [];
            
            // Filter by type (default: AS + LI)
            $typeParam = request('type', 'AS+LI');
            if ($typeParam === 'AS+LI') {
                $options['type'] = ['AS', 'LI'];
            } else {
                $options['type'] = $typeParam;
            }

            // Filter by active status
            if (request()->boolean('include_inactive')) {
                $options['include_inactive'] = true;
            }

            // Filter by minimum balance
            if (request()->filled('min_balance')) {
                $options['min_balance'] = request('min_balance');
            }

            // Call model method instead of raw query
            $total = $user->totalLiquidAsset($options);

            // Response format
            $response = [
                'status'             => 'success',
                'user_id'            => $user->id,
                'name'               => $user->name,
                'total_liquid_asset' => $total,
                'filters'            => [
                    'type'             => $typeParam,
                    'include_inactive' => request()->boolean('include_inactive'),
                    'min_balance'      => request('min_balance'),
                ],
                'generated_at'       => now()->toIso8601String(),
            ];

            // Add formatted version if requested
            if (request('format') !== 'json') {
                $response['formatted'] = $this->rupiah($total);
            }

            return response()->json($response, 200);

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

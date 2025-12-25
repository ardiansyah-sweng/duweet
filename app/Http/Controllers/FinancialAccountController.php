<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialAccountController extends Controller
{
    /**
     * Get liquid assets summary for a specific user_account_id
     *
     * @param int $user_account_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLiquidAssets($user_account_id)
    {
        try {
            $summary = UserFinancialAccount::getAllUsersLiquidAssetsQuery($user_account_id);
            $item = $summary[0] ?? null;
            if (!$item) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'User liquid asset not found'
                ], 404);
            }

            // Ambil nama user dari relasi user_account -> user
            $userAccount = \App\Models\UserAccount::where('id', $item->user_account_id)->first();
            $userName = null;
            $userId = null;
            if ($userAccount) {
                $user = $userAccount->user;
                $userName = $user ? $user->name : null;
                $userId = $userAccount->id;
            }

            // Ambil status is_liquid dan type dari salah satu financial_account yang terkait
            $isLiquid = null;
            $type = null;
            if (isset($item->user_account_id)) {
                $ufa = \App\Models\UserFinancialAccount::where('user_account_id', $item->user_account_id)
                    ->where('is_active', 1)
                    ->first();
                if ($ufa) {
                    $fa = \App\Models\FinancialAccount::find($ufa->financial_account_id);
                    $isLiquid = $fa ? (bool) $fa->is_liquid : null;
                    $type = $fa ? $fa->type : null;
                }
            }

            return response()->json([
                'status' => 'success',
                'user_id' => $userId,
                'name' => $userName,
                'total_liquid_asset' => (int) $item->total_liquid_assets,
                'formatted' => 'Rp ' . number_format($item->total_liquid_assets, 0, ',', '.'),
                'is_liquid' => $isLiquid,
                'type' => $type,
                'generated_at' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveAccounts(Request $request)
    {
        // Buat objek
        $model = new FinancialAccount(); 
        $activeAccounts = $model->getActiveAccounts(); 
        return response()->json([
            'success' => true,
            'message' => 'Daftar Akun Keuangan yang Aktif',
            'count' => count($activeAccounts),
            'data' => $activeAccounts
        ]);
    }

    public function show($id)
    {
        try {
            $model = new FinancialAccount();
            $data = $model->getById($id);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'FinancialAccount tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get liquid assets summary for all users (using pure DML query from model)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsersLiquidAssets()
    {
        try {
            // Memanggil query DML dari model
            $summary = UserFinancialAccount::getAllUsersLiquidAssetsQuery();
            
            // Format hasil query
            $formattedSummary = array_map(function ($item) {
                return [
                    'user_account_id' => $item->user_account_id,
                    'total_liquid_assets' => (int) $item->total_liquid_assets,
                    'formatted' => 'Rp ' . number_format($item->total_liquid_assets, 0, ',', '.')
                ];
            }, $summary);
            
            return response()->json([
                'success' => true,
                'message' => 'All users liquid assets retrieved successfully',
                'data' => [
                    'summary' => $formattedSummary,
                    'total_users' => count($summary)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
        

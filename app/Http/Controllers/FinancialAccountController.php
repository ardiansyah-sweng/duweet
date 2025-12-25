<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialAccountController extends Controller
{
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
        

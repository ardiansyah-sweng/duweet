<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
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
}
        

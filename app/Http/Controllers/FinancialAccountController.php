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
        // Menggunakan scopeActive() dari model
        $activeAccounts = FinancialAccount::active()->get();

        return response()->json([
            'message' => 'Daftar Akun Keuangan yang Aktif',
            'count' => $activeAccounts->count(),
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
        

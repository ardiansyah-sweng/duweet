<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialAccountController extends Controller
{
public function getActiveAccounts(Request $request) // Nama method yang dipanggil route
    {
        // 1. Panggil method Raw SQL di Model
        $activeAccounts = FinancialAccount::getActiveAccounts();

        // 2. Perbaikan: Gunakan fungsi count() PHP karena hasilnya adalah array
        $count = count($activeAccounts); 

        return response()->json([
            'message' => 'Daftar Akun Keuangan yang Aktif (Raw SQL)',
            'count' => $count, // Menggunakan variabel $count yang sudah benar
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
        

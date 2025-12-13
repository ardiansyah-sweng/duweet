<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialAccountController extends Controller
{
   
    public function index(Request $request)
    {
        try {
            $model = new FinancialAccount();
            $result = $model->getAll(
                q: $request->input('q'),
                type: $request->input('type'),
                perPage: (int) $request->input('per_page', 10),
                page: (int) $request->input('page', 1)
            );

            return response()->json([
                'success' => true,
                'data' => $result['records'],
                'pagination' => [
                    'page' => (int) $request->input('page', 1),
                    'per_page' => (int) $request->input('per_page', 10),
                    'total' => $result['total'],
                    'last_page' => ceil($result['total'] / (int) $request->input('per_page', 10))
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

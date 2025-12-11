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
            
            
            $model= new FinancialAccount();

            $result= $model->getIndexData(
                $q = $request->input('q'),
                $type = $request->input('type'),
                $perPage = (int) $request->input('per_page', 10),
                $page = (int) $request->input('page', 1)
            );
            // Build WHERE clause
           
            
            return response()->json([
                'success' => true,
                'data' => $result['records'],
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => (int) $result['total'],
                    'last_page' => ceil($result['total'] / $perPage)
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
        $table = config('db_tables.financial_account', 'financial_accounts');
        $model=new FinancialAccount();
        $data= $model->getById($id);

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
    }


   
    public function searchByType(Request $request)
    {
        try {
            $type = $request->input('type');
            
            // Validasi input
            if (empty($type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter type diperlukan (IN, EX, SP, LI, AS)',
                    'count' => 0,
                    'data' => []
                ], 400);
            }

            $model = new FinancialAccount();
            $result = $model->getByType(
                $type=$request->input('type'),
                $isActive = $request->input('is_active'),
                $perPage = (int) $request->input('per_page', 10),
                $page = (int) $request->input('page', 1)
            );
            
            // Build WHERE clause
          
            
            return response()->json([
                'success' => true,
                'type' => $type,
                'count' => $result['total'],
                'data' => $result['records'],
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $result['total'],
                    'last_page' => ceil($result['total'] / $perPage)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'count' => 0,
                'data' => []
            ], 500);
        }
    }

    
     // API: Search financial accounts by ID (single atau multiple)
 
    public function searchById(Request $request)
    {
        try {
           
            $idsParam = $request->input('ids');
            $model=new FinancialAccount();

            $ids = array_filter(array_map('trim', explode(',', $idsParam)), 'strlen');
            $ids = array_values(array_filter($ids, 'is_numeric'));
            $result= $model->getMultipleId($ids);   

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id diperlukan',
                    'count' => 0,
                    'data' => []
                ], 400);
            }

            // Support single id atau multiple ids (dipisah koma)
            

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id tidak valid',
                    'count' => 0,
                    'data' => []
                ], 400);
            }
            
            

            if (empty($results)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun dengan ID tersebut tidak ditemukan',
                    'count' => 0,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'search_type' => count($ids) > 1 ? 'multiple' : 'single',
                'ids' => $ids,
                'count' => count($results),
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'count' => 0,
                'data' => []
            ], 500);
        }
    }

}

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
            $table = config('db_tables.financial_account', 'financial_accounts');
            
            // Get query parameters
            $q = $request->input('q');
            $type = $request->input('type');
            $perPage = (int) $request->input('per_page', 10);
            $page = (int) $request->input('page', 1);
            
            // Build WHERE clause
            $wheres = [];
            $params = [];
            
            if (!empty($q)) {
                $wheres[] = "(name LIKE ? OR description LIKE ?)";
                $params[] = "%$q%";
                $params[] = "%$q%";
            }
            
            if (!empty($type)) {
                $wheres[] = "type = ?";
                $params[] = $type;
            }
            
            $whereClause = !empty($wheres) ? "WHERE " . implode(" AND ", $wheres) : "";
            
            // Count total records
            $countSql = "SELECT COUNT(*) as total FROM $table $whereClause";
            $countResult = DB::select($countSql, $params);
            $total = $countResult[0]->total;
            
            // Get paginated records
            $offset = ($page - 1) * $perPage;
            $dataSql = "SELECT * FROM $table $whereClause ORDER BY sort_order ASC LIMIT $perPage OFFSET $offset";
            $records = DB::select($dataSql, $params);
            
            return response()->json([
                'success' => true,
                'data' => $records,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => (int) $total,
                    'last_page' => ceil($total / $perPage)
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

        $sql = "SELECT * FROM $table WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);

        if (empty($result)) {
            return response()->json([
                'success' => false,
                'message' => 'FinancialAccount tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result[0]
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

            $table = config('db_tables.financial_account', 'financial_accounts');
            $isActive = $request->input('is_active');
            $perPage = (int) $request->input('per_page', 10);
            $page = (int) $request->input('page', 1);
            
            // Build WHERE clause
            $wheres = ["type = ?"];
            $params = [$type];
            
            if ($isActive !== null) {
                $wheres[] = "is_active = ?";
                $params[] = (int) $isActive;
            }
            
            $whereClause = "WHERE " . implode(" AND ", $wheres);
            
            // Count total records dengan type ini
            $countSql = "SELECT COUNT(*) as total FROM $table $whereClause";
            $countResult = DB::select($countSql, $params);
            $total = (int) $countResult[0]->total;
            
            // Get paginated records
            $offset = ($page - 1) * $perPage;
            $dataSql = "SELECT * FROM $table $whereClause ORDER BY sort_order ASC LIMIT $perPage OFFSET $offset";
            $records = DB::select($dataSql, $params);
            
            return response()->json([
                'success' => true,
                'type' => $type,
                'count' => $total,
                'data' => $records,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
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
            $idsParam = $request->input('id');
            $table = config('db_tables.financial_account', 'financial_accounts');

            if (empty($idsParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id diperlukan',
                    'count' => 0,
                    'data' => []
                ], 400);
            }

            // Support single id atau multiple ids (dipisah koma)
            $ids = array_filter(array_map('trim', explode(',', $idsParam)), 'strlen');
            $ids = array_values(array_filter($ids, 'is_numeric'));

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter id tidak valid',
                    'count' => 0,
                    'data' => []
                ], 400);
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT * FROM {$table} WHERE id IN ($placeholders) ORDER BY sort_order ASC";
            $results = DB::select($sql, $ids);

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

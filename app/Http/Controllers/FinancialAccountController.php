<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Http\Request;

class FinancialAccountController extends Controller
{
    /**
     * GET /financial-accounts
     * Default: hanya is_active = true
     * Optional: ?is_active=true|false
     */
    public function index(Request $request)
    {
        try {
            $query = FinancialAccount::query();

            // Jika query param is_active dikirim
            if ($request->has('is_active')) {
                $flag = filter_var(
                    $request->query('is_active'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );

                if (is_null($flag)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid is_active value'
                    ], 422);
                }

                $query->where(FinancialAccountColumns::IS_ACTIVE, $flag);
            } 
            // Default behavior (tugas kamu): Jika tidak ada param, filter HANYA yang active
            else {
                // Perbaikan: Lakukan filter is_active = true secara eksplisit
                $query->where(FinancialAccountColumns::IS_ACTIVE, true);
            }

            $data = $query->get();

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

    public function getActiveAccounts()
    {
        try {
            // Langsung panggil scope 'active' atau where clause eksplisit
            $data = FinancialAccount::active()->get();

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
     * GET /financial-accounts/{id}
     */
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
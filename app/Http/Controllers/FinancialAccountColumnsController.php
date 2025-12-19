<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use Illuminate\Http\Request;

class FinancialAccountColumnsController extends Controller
{
    private function rupiah(int|float $n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    /**
     * GET /api/financial-account/{id}
     * 
     * Get financial account detail
     */
    public function show(int $id)
    {
        try {
            $account = FinancialAccount::getById($id);

            if (!$account) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Financial account tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $account,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/financial-account/{id}/balance
     * 
     * Update balance financial account
     */
    public function updateBalance(Request $request, int $id)
    {
        try {
            // Cek apakah account ada
            $account = FinancialAccount::getById($id);
            
            if (!$account) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Financial account tidak ditemukan',
                ], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'balance' => 'required|numeric|min:0',
                'initial_balance' => 'sometimes|numeric|min:0',
            ]);

            $oldBalance = $account->balance;
            $oldInitialBalance = $account->initial_balance;

            // Update
            $updated = FinancialAccount::updateBalance(
                $id, 
                (int)$validated['balance'],
                isset($validated['initial_balance']) ? (int)$validated['initial_balance'] : null
            );

            if (!$updated) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal update balance',
                ], 500);
            }

            // Get updated data
            $updatedAccount = FinancialAccount::getById($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Balance berhasil diupdate',
                'data' => [
                    'id' => $id,
                    'name' => $updatedAccount->name,
                    'old_balance' => $oldBalance,
                    'new_balance' => $updatedAccount->balance,
                    'old_initial_balance' => $oldInitialBalance,
                    'new_initial_balance' => $updatedAccount->initial_balance,
                    'formatted_old_balance' => $this->rupiah($oldBalance),
                    'formatted_new_balance' => $this->rupiah($updatedAccount->balance),
                ],
                'updated_at' => now()->toIso8601String(),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

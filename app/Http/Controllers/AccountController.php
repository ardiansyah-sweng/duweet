<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Constants\UserColumns;
use App\Constants\AccountColumns;
use App\Constants\FinancialAccountColumns;
use App\Constants\UserFinancialAccountColumns;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validasi input dengan user_account_id
            $validated = $request->validate([
                'user_account_id'    => 'required|integer|exists:user_accounts,id',
                'name'               => 'required|string|max:255',
                'type'               => 'required|string|in:AS,LI',
                'initial_balance'    => 'required|integer|min:0',
                'is_group'           => 'nullable|boolean',
                'is_active'          => 'nullable|boolean',
            ]);

            // Cek duplikat nama account untuk user_account yang sama
            $user_account_id = $validated['user_account_id'];
            $account_name = trim($validated['name']);
            
            $isDuplicate = DB::table('financial_accounts')
                ->join('user_financial_accounts', 'financial_accounts.id', '=', 'user_financial_accounts.financial_account_id')
                ->where('user_financial_accounts.user_account_id', $user_account_id)
                ->where('financial_accounts.name', $account_name)
                ->exists();

            if ($isDuplicate) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Account dengan nama '{$account_name}' sudah ada untuk user_account ini",
                ], 409);
            }

            // Validasi user_account exists
            $userAccount = DB::table('user_accounts')->find($user_account_id);
            if (!$userAccount) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "User account dengan ID {$user_account_id} tidak ditemukan",
                ], 404);
            }

            // Insert ke tabel financial_accounts
            $accountData = [
                'name' => $validated['name'],
                'type' => $validated['type'],
                'initial_balance' => $validated['initial_balance'],
                'is_group' => $validated['is_group'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
            ];
            $accountId = \App\Models\FinancialAccount::insertFinancialAccount($accountData);

            // Insert ke pivot user_financial_accounts jika bukan group
            if (!($validated['is_group'] ?? false)) {
                DB::table('user_financial_accounts')->insert([
                    'user_account_id' => $user_account_id,
                    'financial_account_id' => $accountId,
                    'balance' => $validated['initial_balance'],
                    'initial_balance' => $validated['initial_balance'],
                    'is_active' => $validated['is_active'] ?? true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'status'           => 'success',
                'message'          => 'Financial account berhasil dibuat',
                'user_account_id'  => $user_account_id,
                'account'          => [
                    'id'              => $accountId,
                    'name'            => $validated['name'],
                    'type'            => $validated['type'],
                    'initial_balance' => $validated['initial_balance'],
                    'is_group'        => $validated['is_group'] ?? false,
                    'is_active'       => $validated['is_active'] ?? true,
                ],
                'created_at'       => now()->toIso8601String(),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat financial account: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function index(Request $request)
    {
        $data = FinancialAccount::listWithUserBalances($request->filled('user_id') ? (int)$request->user_id : null);

        return response()->json(['data' => $data], 200);
    }

    public function show(int $id)
    {
        $row = FinancialAccount::findWithUserBalance($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $row], 200);
    }

}
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
        $validated = $request->validate([
            'user_id'         => 'required|integer|exists:users,id',
            'name'            => 'required|string|max:100',
            'type'            => 'required|in:AS,LI,IN,EX,SP',
            'initial_balance' => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'parent_id'       => 'nullable|integer|exists:financial_accounts,id',
            'is_group'        => 'sometimes|boolean',
            // Query parameters untuk kontrol tambahan
            'auto_activate'   => 'sometimes|boolean', // Auto activate account (default: true)
            'return_detail'   => 'sometimes|boolean', // Return detailed info (default: false)
        ]);

        try {
            $financial_account = \App\Models\FinancialAccount::createForUser([
                'user_id'         => $validated['user_id'],
                'name'            => $validated['name'],
                'type'            => $validated['type'],
                'initial_balance' => (int) $validated['initial_balance'],
                'description'     => $validated['description'] ?? null,
                'parent_id'       => $validated['parent_id'] ?? null,
                'is_group'        => (bool)($validated['is_group'] ?? false),
            ]);

            // Response basic
            $response = [
                'status'  => 'success',
                'message' => 'Akun berhasil dibuat',
                'data'    => [
                    'id'              => $financial_account->id,
                    'name'            => $financial_account->name,
                    'type'            => $financial_account->type,
                    'initial_balance' => $financial_account->initial_balance,
                ],
            ];

            // Include detailed info if requested
            if (request()->boolean('return_detail')) {
                $response['data'] = $financial_account->toArray();
                $response['data']['pivot'] = \App\Models\FinancialAccount::getUserPivot($financial_account->id, $validated['user_id']);
                    
                // Include parent info if exists
                if ($financial_account->parent_id) {
                    $response['data']['parent'] = FinancialAccount::find($financial_account->parent_id);
                }
            }

            return response()->json($response, 201);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
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

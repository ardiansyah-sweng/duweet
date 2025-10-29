<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ]);

        $account = \App\Models\FinancialAccount::create([
            'name'            => $validated['name'],
            'type'            => $validated['type'],
            'balance'         => $validated['initial_balance'],
            'initial_balance' => $validated['initial_balance'],
            'is_group'        => false,
            'description'     => $validated['description'] ?? null,
            'is_active'       => true,
        ]);

        \App\Models\UserFinancialAccount::create([
            'user_id'              => $validated['user_id'],
            'financial_account_id' => $account->id,
            'balance'              => $account->balance,
            'initial_balance'      => $account->initial_balance,
            'is_active'            => true,
        ]);

        // 🔑 RETURN JSON + STATUS 201
        return response()->json([
            'message' => 'Akun berhasil dibuat',
            'data'    => $account,
        ], 201);
    }
    public function index(Request $request)
    {
        // Ganti 'financial_accounts' jika tabelmu bernama 'accounts'
        $q = DB::table('financial_accounts as fa')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                'fa.id','fa.name','fa.type','fa.balance','fa.initial_balance',
                'fa.description','fa.is_active','fa.created_at',
                'ufa.user_id','ufa.balance as user_balance','ufa.initial_balance as user_initial_balance'
            )
            ->orderByDesc('fa.id');

        if ($request->filled('user_id')) {
            $q->where('ufa.user_id', (int) $request->user_id);
        }

        $data = $q->get();

        return response()->json(['data' => $data], 200);
    }

    // (opsional) GET /api/accounts/{id}
    public function show(int $id)
    {
        $row = DB::table('financial_accounts as fa')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                'fa.id','fa.name','fa.type','fa.balance','fa.initial_balance',
                'fa.description','fa.is_active','fa.created_at',
                'ufa.user_id','ufa.balance as user_balance','ufa.initial_balance as user_initial_balance'
            )
            ->where('fa.id', $id)
            ->first();

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $row], 200);
    }

}

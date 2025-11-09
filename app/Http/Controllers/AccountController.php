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

        $financial_account = \App\Models\FinancialAccount::createForUser([
            'user_id'         => $validated['user_id'],
            'name'            => $validated['name'],
            'type'            => $validated['type'],
            'initial_balance' => (int) $validated['initial_balance'],
            'description'     => $validated['description'] ?? null,
            'is_group'        => false,
        ]);

        return response()->json([
            'message' => 'Akun berhasil dibuat',
            'data'    => $financial_account,
        ], 201);
    }
    public function index(Request $request)
    {
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

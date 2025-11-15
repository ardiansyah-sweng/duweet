<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $accounts = Account::all();
        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id'       => 'required|exists:accounts,id',
            'type'             => 'required|in:IN,EX,SP,LI,AS',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            Transaction::create([
                'account_id'       => $request->account_id,
                'type'             => $request->type,
                'amount'           => $request->amount,
                'description'      => $request->description,
                'transaction_date' => $request->transaction_date,
            ]);
        });

        return redirect()->route('transactions.create')
                         ->with('success', 'Transaksi berhasil disimpan.');
    }
}

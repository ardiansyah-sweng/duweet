<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaksi;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Update an existing transaksi and adjust account balances.
     */
    public function update(UpdateTransactionRequest $request, $id)
    {
        $tx = Transaksi::find($id);
        if (! $tx) {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        $data = $request->only(['account_id','date','description','amount','type','meta']);

        // Validate using model rules as well (ensures exists and formats)
        $validator = Transaksi::validator($data, true);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Determine target account (either changed or same)
        $accountId = $data['account_id'] ?? $tx->account_id;
        $account = Account::find($accountId);
        if (! $account) {
            return response()->json(['error' => 'Account tidak ditemukan'], 404);
        }

        // Business rule: account must be leaf
        if ($account->is_group) {
            return response()->json(['error' => 'Account adalah group; tidak boleh menyimpan transaksi langsung'], 422);
        }

        // Perform update atomically
        $result = DB::transaction(function () use ($tx, $data, $account) {
            $oldAmount = $tx->amount;
            $oldAccount = $tx->account_id;

            // Update transaction
            $tx->fill($data);
            $tx->save();

            // If amount or account changed, adjust balances
            if (isset($data['amount']) || isset($data['account_id'])) {
                $newAmount = $tx->amount;
                $oldType = $tx->getOriginal('type') ?? $tx->type;
                $newType = $tx->type;

                // If account changed, reverse old transaction and apply new one
                if ($oldAccount && $oldAccount !== $account->id) {
                    $oldAcc = Account::find($oldAccount);
                    if ($oldAcc) {
                        // Reverse old transaction
                        if ($oldType === 'debit') {
                            $oldAcc->balance -= $oldAmount;
                        } else {
                            $oldAcc->balance += $oldAmount;
                        }
                        $oldAcc->save();
                        $this->recomputeParents($oldAcc);
                    }
                    
                    // Apply new transaction to new account
                    if ($newType === 'debit') {
                        $account->balance += $newAmount;
                    } else {
                        $account->balance -= $newAmount;
                    }
                } else {
                    // Same account: calculate delta
                    $delta = $newAmount - $oldAmount;
                    if ($newType === 'debit') {
                        $account->balance += $delta;
                    } else {
                        $account->balance -= $delta;
                    }
                }

                // Check asset negative rule
                if ($account->type === 'AS' && $account->balance < 0) {
                    throw new \Exception('Saldo asset tidak boleh negatif');
                }

                $account->save();
                $this->recomputeParents($account);
            }

            return $tx;
        });

        if ($result instanceof \Exception) {
            return response()->json(['error' => $result->getMessage()], 422);
        }

        return response()->json(['data' => $result], 200);
    }

    protected function recomputeParents(Account $account)
    {
        $parent = $account->parent;
        while ($parent) {
            $parent->recomputeBalanceFromChildren();
            $parent = $parent->parent;
        }
    }
}

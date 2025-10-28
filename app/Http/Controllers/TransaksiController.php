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
        if ($account->{\App\Constants\AccountColumns::IS_GROUP}) {
            return response()->json(['error' => 'Account adalah group; tidak boleh menyimpan transaksi langsung'], 422);
        }

        // Perform update atomically
        $result = DB::transaction(function () use ($tx, $data, $account) {
            $oldAmount = $tx->amount;
            $oldAccount = $tx->account_id;

            // Update transaction
            $tx->fill($data);
            $tx->save();

            // If account changed, remove from old account and add to new
            if (isset($data['amount']) || isset($data['account_id'])) {
                $newAmount = $tx->amount;
                $delta = $newAmount - $oldAmount;

                // Adjust balances
                // Deduct from old account
                if ($oldAccount && $oldAccount !== $account->id) {
                    $oldAcc = Account::find($oldAccount);
                    if ($oldAcc) {
                        $oldAcc->{\App\Constants\AccountColumns::BALANCE} -= $oldAmount;
                        $oldAcc->save();
                        // recompute parents
                        $this->recomputeParents($oldAcc);
                    }
                    // Add to new account
                    $account->{\App\Constants\AccountColumns::BALANCE} += $newAmount;
                } else {
                    // Same account: apply delta
                    $account->{\App\Constants\AccountColumns::BALANCE} += $delta;
                }

                // Check asset negative rule
                if ($account->{\App\Constants\AccountColumns::TYPE} === 'AS' && $account->{\App\Constants\AccountColumns::BALANCE} < 0) {
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

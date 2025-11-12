<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * Disable automatic timestamps if the table doesn't have created_at/updated_at
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'financial_account_id',
        'amount',
        'entry_type',
        'transaction_date',
        'description',
    ];

    /**
     * Get the user account that owns the transaction.
     */
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }

    /**
     * Get total transactions per user - simplified version.
     *
     * Returns essential transaction summary per user:
     * - User information (id, name, email)
     * - transaction_count: Number of transactions (count)
     * - total_transactions: Total money from all transactions (sum of amounts)
     * - total_debit & total_credit: Total money per type
     * - net_balance: Net balance (debit - credit)
     *
     * Usage: \App\Models\Transaction::getTotalTransactionsPerUser();
     * Optional parameter: $userId
     *
     * @param  int|null  $userId  Filter by specific user ID
     * @return \Illuminate\Support\Collection
     */
    public static function getTotalTransactionsPerUser(?int $userId = null)
    {
        // Build a subquery that aggregates transactions grouped by the owning user (via user_accounts)
        $txSub = DB::table('transactions as t')
            ->join('user_accounts as ua', 't.' . TransactionColumns::USER_ACCOUNT_ID, '=', 'ua.' . UserAccountColumns::ID)
            ->selectRaw('ua.' . UserAccountColumns::ID_USER . ' as user_id')
            ->selectRaw('COUNT(t.id) as transaction_count')
            ->selectRaw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN 1 ELSE 0 END) as debit_count', ['debit'])
            ->selectRaw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN 1 ELSE 0 END) as credit_count', ['credit'])
            ->selectRaw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_debit', ['debit'])
            ->selectRaw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_credit', ['credit'])
            ->selectRaw('COALESCE(SUM(t.' . TransactionColumns::AMOUNT . '), 0) as total_transactions')
            ->groupBy('ua.' . UserAccountColumns::ID_USER);

        // Wrap subquery as a derived table and join with users
        $query = DB::table('users as u')
            ->leftJoinSub($txSub, 'tx', 'tx.user_id', '=', 'u.id')
            ->select([
                'u.id as user_id',
                'u.name as user_name',
                'u.email as user_email',
                DB::raw('COALESCE(tx.transaction_count, 0) as transaction_count'),
                DB::raw('COALESCE(tx.debit_count, 0) as debit_count'),
                DB::raw('COALESCE(tx.credit_count, 0) as credit_count'),
                DB::raw('COALESCE(tx.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(tx.total_credit, 0) as total_credit'),
                DB::raw('COALESCE(tx.total_transactions, 0) as total_transactions'),
                DB::raw('COALESCE(tx.total_debit, 0) - COALESCE(tx.total_credit, 0) as net_balance'),
            ])
            ->orderByDesc('total_transactions');

        if ($userId !== null) {
            $query->where('u.id', $userId);
        }

        return $query->get();
    }
}

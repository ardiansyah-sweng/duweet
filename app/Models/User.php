<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Constants\TransactionColumns;
use App\Constants\FinancialAccountColumns;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the transactions for this user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Static helper: get total transactions per user with flexible filtering
     * 
     * @param array|null $userIds - filter by specific user IDs
     * @param string|null $groupBy - 'account-type' to group by account type, null for detailed breakdown
     * @return Collection
     */
    public static function getTotalTransactionStats(?array $userIds = null, ?string $groupBy = null): Collection
    {
        // Resolve table names from config where possible to avoid hard-coded table names
        $usersTable = (new self())->getTable();
        $transactionsTable = config('db_tables.transaction', 'transactions');
    // Use canonical config key for accounts table
    $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        if ($groupBy === 'account-type') {
            // Group by account type
            $query = DB::table($usersTable)
                ->leftJoin($transactionsTable, "$usersTable." . TransactionColumns::ID, '=', "$transactionsTable." . TransactionColumns::USER_ID)
                ->leftJoin($accountsTable, "$transactionsTable." . TransactionColumns::ACCOUNT_ID, '=', "$accountsTable." . FinancialAccountColumns::ID)
                ->select(
                    "$usersTable." . 'id',
                    "$usersTable." . 'name',
                    "$usersTable." . 'email',
                    "$accountsTable." . FinancialAccountColumns::TYPE . ' as account_type',
                    DB::raw('COALESCE(SUM(' . $transactionsTable . '.' . TransactionColumns::AMOUNT . '), 0) as total_amount'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::ID . ' IS NOT NULL THEN 1 ELSE 0 END), 0) as transaction_count'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::BALANCE_EFFECT . ' = "increase" THEN ' . $transactionsTable . '.' . TransactionColumns::AMOUNT . ' WHEN ' . $transactionsTable . '.' . TransactionColumns::BALANCE_EFFECT . ' = "decrease" THEN -' . $transactionsTable . '.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as net_balance_effect'),
                    DB::raw('COALESCE(COUNT(DISTINCT ' . $transactionsTable . '.' . TransactionColumns::TRANSACTION_GROUP_ID . '), 0) as transaction_group_count')
                )
                ->groupBy("$usersTable.id", "$usersTable.name", "$usersTable.email", "$accountsTable." . FinancialAccountColumns::TYPE);
        } else {
            // Default: detailed breakdown (debit/credit)
            $query = DB::table($usersTable)
                ->leftJoin($transactionsTable, "$usersTable." . TransactionColumns::ID, '=', "$transactionsTable." . TransactionColumns::USER_ID)
                ->select(
                    "$usersTable." . 'id',
                    "$usersTable." . 'name',
                    "$usersTable." . 'email',
                    DB::raw('COALESCE(SUM(' . $transactionsTable . '.' . TransactionColumns::AMOUNT . '), 0) as total_transaction_amount'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN ' . $transactionsTable . '.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_debit'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN ' . $transactionsTable . '.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_credit'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::ID . ' IS NOT NULL THEN 1 ELSE 0 END), 0) as transaction_count'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN 1 ELSE 0 END), 0) as debit_count'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN 1 ELSE 0 END), 0) as credit_count'),
                    DB::raw('COALESCE(SUM(CASE WHEN ' . $transactionsTable . '.' . TransactionColumns::BALANCE_EFFECT . ' = "increase" THEN ' . $transactionsTable . '.' . TransactionColumns::AMOUNT . ' WHEN ' . $transactionsTable . '.' . TransactionColumns::BALANCE_EFFECT . ' = "decrease" THEN -' . $transactionsTable . '.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as net_balance_effect'),
                    DB::raw('COALESCE(COUNT(DISTINCT ' . $transactionsTable . '.' . TransactionColumns::TRANSACTION_GROUP_ID . '), 0) as transaction_group_count')
                )
                ->groupBy("$usersTable.id", "$usersTable.name", "$usersTable.email");
        }

        if (!empty($userIds)) {
            $query->whereIn("$usersTable.id", $userIds);
        }

        return $query->get();
    }
}

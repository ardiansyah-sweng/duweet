<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;

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
     * Get total transactions per user with detailed breakdown.
     *
     * Returns:
     * - Total transactions count
     * - Total debit amount
     * - Total credit amount
     * - Net balance (debit - credit)
     * - Total amount (sum of all transactions)
     * - Debit transactions count
     * - Credit transactions count
     *
     * Usage: \App\Models\User::transactionTotals();
     * Optional parameters: $from, $to (date range strings)
     *
     * @param  string|null  $from
     * @param  string|null  $to
     * @return \Illuminate\Support\Collection
     */
    public static function transactionTotals(
        ?string $from = null, 
        ?string $to = null,
        ?int $userId = null,
        ?bool $groupByAccountType = false
    )
    {
        $financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
        $driver = DB::connection()->getDriverName();
        
        // Fungsi untuk menghitung selisih hari (compatible dengan SQLite dan MySQL)
        $dateDiffDays = $driver === 'sqlite' 
            ? 'CAST((julianday(MAX(t.' . TransactionColumns::CREATED_AT . ')) - julianday(MIN(t.' . TransactionColumns::CREATED_AT . '))) AS INTEGER)'
            : 'DATEDIFF(MAX(t.' . TransactionColumns::CREATED_AT . '), MIN(t.' . TransactionColumns::CREATED_AT . '))';
        
        $dateDiffForAvg = $driver === 'sqlite'
            ? '(julianday(MAX(t.' . TransactionColumns::CREATED_AT . ')) - julianday(MIN(t.' . TransactionColumns::CREATED_AT . ')))'
            : 'DATEDIFF(MAX(t.' . TransactionColumns::CREATED_AT . '), MIN(t.' . TransactionColumns::CREATED_AT . '))';
        
        // Build select fields
        $selectFields = [
            // User Information
            'u.id as user_id',
            'u.name as user_name',
            'u.email as user_email',
            'u.created_at as user_registered_at',
            
            // User Account Information
            'ua.id as user_account_id',
            'ua.username',
            'ua.is_active as account_is_active',
            DB::raw('ua.' . UserAccountColumns::VERIFIED_AT . ' as account_verified_at'),
        ];
        
        // Add financial account fields if grouping by account type
        if ($groupByAccountType) {
            $selectFields[] = 'fa.id as financial_account_id';
            $selectFields[] = 'fa.name as financial_account_name';
            $selectFields[] = 'fa.type as financial_account_type';
        }
        
        // Add aggregation fields
        $selectFields = array_merge($selectFields, [
            // Transaction Counts
            DB::raw('COUNT(DISTINCT t.id) as total_transactions'),
            DB::raw('COUNT(DISTINCT t.' . TransactionColumns::TRANSACTION_GROUP_ID . ') as total_transaction_groups'),
            DB::raw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN 1 ELSE 0 END) as debit_transactions'),
            DB::raw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN 1 ELSE 0 END) as credit_transactions'),
            
            // Transaction Amounts
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_debit_amount'),
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_credit_amount'),
            DB::raw('COALESCE(SUM(t.' . TransactionColumns::AMOUNT . '), 0) as total_amount'),
            
            // Net Balance & Statistics
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as net_balance'),
            DB::raw('COALESCE(AVG(t.' . TransactionColumns::AMOUNT . '), 0) as avg_transaction_amount'),
            DB::raw('COALESCE(MIN(t.' . TransactionColumns::AMOUNT . '), 0) as min_transaction_amount'),
            DB::raw('COALESCE(MAX(t.' . TransactionColumns::AMOUNT . '), 0) as max_transaction_amount'),
            
            // Balance Effect Breakdown
            DB::raw('SUM(CASE WHEN t.' . TransactionColumns::BALANCE_EFFECT . ' = "increase" THEN 1 ELSE 0 END) as increase_transactions'),
            DB::raw('SUM(CASE WHEN t.' . TransactionColumns::BALANCE_EFFECT . ' = "decrease" THEN 1 ELSE 0 END) as decrease_transactions'),
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::BALANCE_EFFECT . ' = "increase" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_increase_amount'),
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::BALANCE_EFFECT . ' = "decrease" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_decrease_amount'),
            
            // Financial Account Usage
            DB::raw('COUNT(DISTINCT t.' . TransactionColumns::FINANCIAL_ACCOUNT_ID . ') as unique_accounts_used'),
            
            // Transaction Date Range (compatible with SQLite and MySQL)
            DB::raw('MIN(t.' . TransactionColumns::CREATED_AT . ') as first_transaction_date'),
            DB::raw('MAX(t.' . TransactionColumns::CREATED_AT . ') as last_transaction_date'),
            DB::raw($dateDiffDays . ' as transaction_period_days'),
            
            // Activity Metrics (compatible with SQLite and MySQL)
            DB::raw('CASE 
                WHEN COUNT(t.id) > 0 AND ' . $dateDiffForAvg . ' > 0 
                THEN ROUND(COUNT(t.id) / ' . $dateDiffForAvg . ', 2)
                ELSE 0 
            END as avg_transactions_per_day')
        ]);
        
        // Build group by fields
        $groupByFields = [
            'u.id', 
            'u.name', 
            'u.email', 
            'u.created_at',
            'ua.id', 
            'ua.username', 
            'ua.is_active',
            'ua.' . UserAccountColumns::VERIFIED_AT
        ];
        
        // Add financial account fields to group by if grouping by account type
        if ($groupByAccountType) {
            $groupByFields[] = 'fa.id';
            $groupByFields[] = 'fa.name';
            $groupByFields[] = 'fa.type';
        }
        
        $query = DB::table('users as u')
            ->leftJoin('user_accounts as ua', 'ua.' . UserAccountColumns::ID_USER, '=', 'u.id')
            ->leftJoin('transactions as t', 't.' . TransactionColumns::USER_ACCOUNT_ID, '=', 'ua.id')
            ->leftJoin($financialAccountTable . ' as fa', 'fa.id', '=', 't.' . TransactionColumns::FINANCIAL_ACCOUNT_ID)
            ->select($selectFields)
            ->groupBy($groupByFields)
            ->orderByDesc('total_amount');

        // Filter by user ID if provided
        if ($userId !== null) {
            $query->where('u.id', $userId);
        }

        // Filter by date range if provided
        if ($from !== null && $to !== null) {
            $query->whereBetween('t.' . TransactionColumns::CREATED_AT, [$from, $to]);
        }

        return $query->get();
    }
}

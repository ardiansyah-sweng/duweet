<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\FinancialAccountColumns;
use App\Enums\AccountType;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        TransactionColumns::TRANSACTION_GROUP_ID,
        TransactionColumns::USER_ACCOUNT_ID,
        TransactionColumns::FINANCIAL_ACCOUNT_ID,
        TransactionColumns::ENTRY_TYPE,
        TransactionColumns::AMOUNT,
        TransactionColumns::BALANCE_EFFECT,
        TransactionColumns::DESCRIPTION,
        TransactionColumns::IS_BALANCE,
    ];

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
    ];

    /**
     * Relationship to UserAccount
     */
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, TransactionColumns::USER_ACCOUNT_ID);
    }

    /**
     * Relationship to FinancialAccount
     */
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, TransactionColumns::FINANCIAL_ACCOUNT_ID);
    }

    /**
     * Sum income by period for admin
     * 
     * @param string $period - 'daily', 'weekly', 'monthly', 'yearly'
     * @param string|null $startDate - Start date filter (Y-m-d format)
     * @param string|null $endDate - End date filter (Y-m-d format)
     * @param int|null $userId - Optional user filter
     * @return \Illuminate\Support\Collection
     */
    public static function sumIncomeByPeriod(
        string $period = 'monthly',
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $userId = null
    ) {
        $financialAccountTable = config('db_tables.financial_account');
        $transactionTable = config('db_tables.transaction');
        $userAccountTable = config('db_tables.user_account');

        // Define date format based on period
        $dateFormat = match($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-W%u',
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m',
        };

        $query = DB::table($transactionTable . ' as t')
            ->join($financialAccountTable . ' as fa', 
                't.' . TransactionColumns::FINANCIAL_ACCOUNT_ID, 
                '=', 
                'fa.' . FinancialAccountColumns::ID
            )
            ->leftJoin($userAccountTable . ' as ua',
                't.' . TransactionColumns::USER_ACCOUNT_ID,
                '=',
                'ua.id'
            )
            ->where('fa.' . FinancialAccountColumns::TYPE, AccountType::INCOME->value)
            ->where('t.' . TransactionColumns::ENTRY_TYPE, 'credit')
            ->select([
                DB::raw("DATE_FORMAT(t." . TransactionColumns::CREATED_AT . ", '{$dateFormat}') as period"),
                DB::raw("SUM(t." . TransactionColumns::AMOUNT . ") as total_income"),
                DB::raw("COUNT(DISTINCT t." . TransactionColumns::TRANSACTION_GROUP_ID . ") as transaction_count"),
                DB::raw("COUNT(DISTINCT t." . TransactionColumns::USER_ACCOUNT_ID . ") as user_count"),
            ])
            ->groupBy('period')
            ->orderBy('period', 'desc');

        // Apply date filters
        if ($startDate) {
            $query->where('t.' . TransactionColumns::CREATED_AT, '>=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $query->where('t.' . TransactionColumns::CREATED_AT, '<=', $endDate . ' 23:59:59');
        }

        // Apply user filter
        if ($userId) {
            $query->join('users as u', 'ua.user_id', '=', 'u.id')
                ->where('u.id', $userId);
        }

        return $query->get();
    }

    /**
     * Sum income by financial account category
     * 
     * @param string|null $startDate - Start date filter (Y-m-d format)
     * @param string|null $endDate - End date filter (Y-m-d format)
     * @param int|null $userId - Optional user filter
     * @return \Illuminate\Support\Collection
     */
    public static function sumIncomeByCategory(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $userId = null
    ) {
        $financialAccountTable = config('db_tables.financial_account');
        $transactionTable = config('db_tables.transaction');
        $userAccountTable = config('db_tables.user_account');

        $query = DB::table($transactionTable . ' as t')
            ->join($financialAccountTable . ' as fa', 
                't.' . TransactionColumns::FINANCIAL_ACCOUNT_ID, 
                '=', 
                'fa.' . FinancialAccountColumns::ID
            )
            ->leftJoin($userAccountTable . ' as ua',
                't.' . TransactionColumns::USER_ACCOUNT_ID,
                '=',
                'ua.id'
            )
            ->where('fa.' . FinancialAccountColumns::TYPE, AccountType::INCOME->value)
            ->where('t.' . TransactionColumns::ENTRY_TYPE, 'credit')
            ->select([
                'fa.' . FinancialAccountColumns::ID . ' as account_id',
                'fa.' . FinancialAccountColumns::NAME . ' as account_name',
                'fa.' . FinancialAccountColumns::PARENT_ID . ' as parent_id',
                DB::raw("SUM(t." . TransactionColumns::AMOUNT . ") as total_income"),
                DB::raw("COUNT(DISTINCT t." . TransactionColumns::TRANSACTION_GROUP_ID . ") as transaction_count"),
            ])
            ->groupBy('account_id', 'account_name', 'parent_id')
            ->orderBy('total_income', 'desc');

        // Apply date filters
        if ($startDate) {
            $query->where('t.' . TransactionColumns::CREATED_AT, '>=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $query->where('t.' . TransactionColumns::CREATED_AT, '<=', $endDate . ' 23:59:59');
        }

        // Apply user filter
        if ($userId) {
            $query->join('users as u', 'ua.user_id', '=', 'u.id')
                ->where('u.id', $userId);
        }

        return $query->get();
    }

    /**
     * Get income summary with detailed breakdown
     * 
     * @param string|null $startDate - Start date filter (Y-m-d format)
     * @param string|null $endDate - End date filter (Y-m-d format)
     * @param int|null $userId - Optional user filter
     * @return array
     */
    public static function getIncomeSummary(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $userId = null
    ): array {
        $financialAccountTable = config('db_tables.financial_account');
        $transactionTable = config('db_tables.transaction');
        $userAccountTable = config('db_tables.user_account');

        $query = DB::table($transactionTable . ' as t')
            ->join($financialAccountTable . ' as fa', 
                't.' . TransactionColumns::FINANCIAL_ACCOUNT_ID, 
                '=', 
                'fa.' . FinancialAccountColumns::ID
            )
            ->leftJoin($userAccountTable . ' as ua',
                't.' . TransactionColumns::USER_ACCOUNT_ID,
                '=',
                'ua.id'
            )
            ->where('fa.' . FinancialAccountColumns::TYPE, AccountType::INCOME->value)
            ->where('t.' . TransactionColumns::ENTRY_TYPE, 'credit');

        // Apply date filters
        if ($startDate) {
            $query->where('t.' . TransactionColumns::CREATED_AT, '>=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $query->where('t.' . TransactionColumns::CREATED_AT, '<=', $endDate . ' 23:59:59');
        }

        // Apply user filter
        if ($userId) {
            $query->join('users as u', 'ua.user_id', '=', 'u.id')
                ->where('u.id', $userId);
        }

        $summary = $query->select([
            DB::raw("SUM(t." . TransactionColumns::AMOUNT . ") as total_income"),
            DB::raw("COUNT(DISTINCT t." . TransactionColumns::TRANSACTION_GROUP_ID . ") as total_transactions"),
            DB::raw("COUNT(DISTINCT t." . TransactionColumns::USER_ACCOUNT_ID . ") as total_users"),
            DB::raw("AVG(t." . TransactionColumns::AMOUNT . ") as average_income"),
            DB::raw("MAX(t." . TransactionColumns::AMOUNT . ") as max_income"),
            DB::raw("MIN(t." . TransactionColumns::AMOUNT . ") as min_income"),
        ])->first();

        return [
            'total_income' => $summary->total_income ?? 0,
            'total_transactions' => $summary->total_transactions ?? 0,
            'total_users' => $summary->total_users ?? 0,
            'average_income' => $summary->average_income ?? 0,
            'max_income' => $summary->max_income ?? 0,
            'min_income' => $summary->min_income ?? 0,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }
}

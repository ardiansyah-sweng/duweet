<?php

namespace App\Models;

use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.transaction', 'transactions');
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->{TransactionColumns::TRANSACTION_GROUP_ID})) {
                $transaction->{TransactionColumns::TRANSACTION_GROUP_ID} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke UserAccount
     */
    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, TransactionColumns::USER_ACCOUNT_ID);
    }

    /**
     * Relasi ke FinancialAccount
     */
    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, TransactionColumns::FINANCIAL_ACCOUNT_ID);
    }

    /**
     * Ambil ringkasan total pendapatan berdasarkan periode (Bulan) untuk user tertentu.
     * Ini adalah implementasi dari query: "sum income user by periode" dengan DML SQL murni.
     *
     * @param int $userAccountId
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        try {
            $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = 'mysql';
        }

        if ($driver === 'sqlite') {
            $periodeExpr = "strftime('%Y-%m', t.created_at)";
        } elseif ($driver === 'pgsql' || $driver === 'postgres') {
            $periodeExpr = "to_char(t.created_at, 'YYYY-MM')";
        } else {
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')";
        }

        $sql = "
            SELECT 
                {$periodeExpr} AS periode,
                COALESCE(SUM(t.amount), 0) AS total_income
            FROM {$transactionsTable} t
            INNER JOIN {$accountsTable} fa ON t.financial_account_id = fa.id
            WHERE 
                t.user_account_id = ?
                AND fa.type = 'IN'
                AND t.balance_effect = 'increase'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY {$periodeExpr}
            ORDER BY periode ASC
        ";

        $rows = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows);
    }

    /**
     * Get total transactions per user account using raw SQL query.
     *
     * @param  int|null  $userAccountId  Filter by specific user account ID
     * @return \Illuminate\Support\Collection
     */
    public static function getTotalTransactionsPerUserAccount(?int $userAccountId = null)
    {
        $transactionTable = config('db_tables.transaction');
        $userAccountTable = config('db_tables.user_account');

        $userAccountIdCol = TransactionColumns::USER_ACCOUNT_ID;
        $transactionGroupIdCol = TransactionColumns::TRANSACTION_GROUP_ID;

        $whereClause = '';
        $bindings = [];
        
        if ($userAccountId !== null) {
            $whereClause = "WHERE user_accounts.id = ?";
            $bindings[] = $userAccountId;
        }

        $sql = "
            SELECT 
                user_accounts.id AS user_account_id,
                user_accounts.email AS user_account_email,
                COUNT(DISTINCT transactions.{$transactionGroupIdCol}) AS transaction_count
            FROM {$userAccountTable} AS user_accounts
            LEFT JOIN {$transactionTable} AS transactions 
                ON user_accounts.id = transactions.{$userAccountIdCol}
            {$whereClause}
            GROUP BY user_accounts.id, user_accounts.email
            ORDER BY transaction_count DESC, user_accounts.id ASC
        ";

        $results = DB::select($sql, $bindings);
        return collect($results);
    }

    /**
     * Ambil detail transaksi lengkap via JOIN
     */
    public static function getDetailById($id)
    {
        return self::query()
        ->from('transactions as t')
        ->join('user_accounts as ua', 'ua.id', '=', 't.user_account_id')
        ->join('users as u', 'u.id', '=', 'ua.id_user')
        ->join('financial_accounts as fa', 'fa.id', '=', 't.financial_account_id')
        ->select(
            't.id as transaction_id',
            't.transaction_group_id',
            't.amount',
            't.entry_type',
            't.balance_effect',
            't.is_balance',
            't.description',
            't.created_at as transaction_date',
            'ua.id as user_account_id',
            'ua.username as user_account_username',
            'ua.email as user_account_email',
            'u.id as id_user',
            'u.name as user_name',
            'fa.id as financial_account_id',
            'fa.name as financial_account_name'
        )
        ->where('t.id', $id)
        ->first();
    }

    /**
     * Scope: Filter transactions by date range (period)
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate->toDateString() : $startDate;
        $endDate = $endDate instanceof Carbon ? $endDate->toDateString() : $endDate;

        return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    /**
     * Scope: Filter transactions by user account
     */
    public function scopeByUserAccount($query, $userAccountId)
    {
        return $query->where(TransactionColumns::USER_ACCOUNT_ID, $userAccountId);
    }

    /**
     * Scope: Filter transactions by financial account
     */
    public function scopeByFinancialAccount($query, $financialAccountId)
    {
        return $query->where(TransactionColumns::FINANCIAL_ACCOUNT_ID, $financialAccountId);
    }

    /**
     * Get all transactions with optional filters using raw SQL
     */
    public static function getAllTransactions(
        ?int $userAccountId = null,
        ?int $financialAccountId = null,
        ?string $entryType = null
    ): \Illuminate\Support\Collection {
        $transactionTable = config('db_tables.transaction', 'transactions');

        $sql = "SELECT * FROM {$transactionTable} WHERE 1=1";
        $bindings = [];

        if ($userAccountId !== null) {
            $sql .= " AND " . TransactionColumns::USER_ACCOUNT_ID . " = ?";
            $bindings[] = $userAccountId;
        }

        if ($financialAccountId !== null) {
            $sql .= " AND " . TransactionColumns::FINANCIAL_ACCOUNT_ID . " = ?";
            $bindings[] = $financialAccountId;
        }

        if ($entryType !== null) {
            $sql .= " AND " . TransactionColumns::ENTRY_TYPE . " = ?";
            $bindings[] = $entryType;
        }

        $sql .= " ORDER BY created_at DESC";

        $results = DB::select($sql, $bindings);
        return collect($results);
    }

    /**
     * Filter transactions by period using raw SQL
     */
    public static function filterTransactionsByPeriod(
        string $startDate,
        string $endDate,
        ?int $userAccountId = null,
        ?int $financialAccountId = null,
        ?string $entryType = null
    ): \Illuminate\Support\Collection {
        $transactionTable = config('db_tables.transaction', 'transactions');

        $sql = "SELECT * FROM {$transactionTable} WHERE created_at BETWEEN ? AND ?";
        $bindings = [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ];

        if ($userAccountId !== null) {
            $sql .= " AND " . TransactionColumns::USER_ACCOUNT_ID . " = ?";
            $bindings[] = $userAccountId;
        }

        if ($financialAccountId !== null) {
            $sql .= " AND " . TransactionColumns::FINANCIAL_ACCOUNT_ID . " = ?";
            $bindings[] = $financialAccountId;
        }

        if ($entryType !== null) {
            $sql .= " AND " . TransactionColumns::ENTRY_TYPE . " = ?";
            $bindings[] = $entryType;
        }

        $sql .= " ORDER BY created_at DESC";

        $results = DB::select($sql, $bindings);
        return collect($results);
    }

    /**
     * Scope: Filter transactions by entry type
     */
    public function scopeByEntryType($query, $entryType)
    {
        return $query->where(TransactionColumns::ENTRY_TYPE, $entryType);
    }
}

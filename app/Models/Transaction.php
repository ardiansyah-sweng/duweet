<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;
    protected $table;

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
        TransactionColumns::CREATED_AT => 'datetime',
        TransactionColumns::UPDATED_AT => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.transaction', 'transactions');
        $this->fillable = TransactionColumns::getFillable();
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
     * Get the fillable attributes for the model.
     */
    public function getFillable()
    {
        return TransactionColumns::getFillable();
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
     * Scope: Filter by user account
     */
    public function scopeByUserAccount($query, $userAccountId)
    {
        return $query->where(TransactionColumns::USER_ACCOUNT_ID, $userAccountId);
    }

    /**
     * Scope: Filter by financial account
     */
    public function scopeByFinancialAccount($query, $financialAccountId)
    {
        return $query->where(TransactionColumns::FINANCIAL_ACCOUNT_ID, $financialAccountId);
    }

    /**
     * Scope: Filter by entry type (debit/credit)
     */
    public function scopeByEntryType($query, $entryType)
    {
        return $query->where(TransactionColumns::ENTRY_TYPE, $entryType);
    }

    /**
     * Scope: Filter by transaction group
     */
    public function scopeByTransactionGroup($query, $groupId)
    {
        return $query->where(TransactionColumns::TRANSACTION_GROUP_ID, $groupId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $query->whereDate(TransactionColumns::CREATED_AT, '>=', $startDate);
        
        if ($endDate) {
            $query->whereDate(TransactionColumns::CREATED_AT, '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Scope: Only balance transactions
     */
    public function scopeBalanceOnly($query)
    {
        return $query->where(TransactionColumns::IS_BALANCE, true);
    }

    /**
     * Scope: Exclude balance transactions
     */
    public function scopeExcludeBalance($query)
    {
        return $query->where(TransactionColumns::IS_BALANCE, false);
    }

    // protected static function booted()
    // {
    //     static::creating(function ($transaction) {
    //         if (empty($transaction->transaction_group_id)) {
    //             $transaction->transaction_group_id = (string) Str::uuid();
    //         }
    //     });
    // }


    /**
     * Ambil ringkasan total pendapatan berdasarkan periode (Bulan) untuk user tertentu.
     * Ini adalah implementasi dari query: "sum income user by periode" dengan DML SQL murni.
     */
    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        // Tentukan fungsi format tanggal berdasarkan driver database
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
     * Hard delete semua transaksi berdasarkan kumpulan user_account_id
     *
     * @param \Illuminate\Support\Collection|array $userAccountIds
     * @return int jumlah row terhapus
     */
    public static function deleteByUserAccountIds($userAccountIds): int
    {
        if (empty($userAccountIds) || count($userAccountIds) === 0) {
            return 0;
        }

        return DB::table((new self)->getTable())
            ->whereIn('user_account_id', $userAccountIds)
            ->delete();
    }

    /**
     * Hard delete semua transaksi milik user (berdasarkan user_id)
     *
     * @param int $userId
     * @return int
     */ 
    public static function deleteByUserId(int $userId): int
    {
        $userAccountIds = DB::table('user_accounts')
            ->where('id_user', $userId)
            ->pluck('id');

        return self::deleteByUserAccountIds($userAccountIds);
    }
    /**
     * Get total transactions per user account using raw SQL query.
     *
     * Returns transaction summary per user account:
     * - user_account_id: User account ID
     * - user_account_email: User account email
     * - transaction_count: Count of unique transaction groups (DISTINCT transaction_group_id)
     *
     * Usage: \App\Models\Transaction::getTotalTransactionsPerUserAccount();
     * Optional parameter: $userAccountId (filter by user account ID)
     *
     * @param  int|null  $userAccountId  Filter by specific user account ID
     * @return \Illuminate\Support\Collection
     */
    public static function getTotalTransactionsPerUserAccount(?int $userAccountId = null)
    {
        // Get table names from config
        $transactionTable = config('db_tables.transaction');
        $userAccountTable = config('db_tables.user_account');

        // Get column names from constants
        $userAccountIdCol = TransactionColumns::USER_ACCOUNT_ID;
        $transactionGroupIdCol = TransactionColumns::TRANSACTION_GROUP_ID;

        // Build WHERE clause for filtering
        $whereClause = '';
        $bindings = [];
        
        if ($userAccountId !== null) {
            $whereClause = "WHERE user_accounts.id = ?";
            $bindings[] = $userAccountId;
        }

        // Raw SQL query - full version without abbreviations
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

        // Execute raw SQL query
        $results = DB::select($sql, $bindings);

        // Convert to collection
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
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|Carbon  $startDate
     * @param  string|Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate->toDateString() : $startDate;
        $endDate = $endDate instanceof Carbon ? $endDate->toDateString() : $endDate;

        return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    /**
     * Get all transactions with optional filters using raw SQL
     * 
     * @param  int|null  $userAccountId
     * @param  int|null  $financialAccountId
     * @param  string|null  $entryType
     * @return \Illuminate\Support\Collection
     */
    public static function getAllTransactions(
        ?int $userAccountId = null,
        ?int $financialAccountId = null,
        ?string $entryType = null
    ): \Illuminate\Support\Collection {
        $transactionTable = config('db_tables.transaction', 'transactions');

        // Start with base SQL
        $sql = "SELECT * FROM {$transactionTable} WHERE 1=1";
        $bindings = [];

        // Add optional filters
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

        // Order by created_at descending
        $sql .= " ORDER BY created_at DESC";

        // Execute raw SQL query
        $results = DB::select($sql, $bindings);

        return collect($results);
    }

    /**
     * Filter transactions by period using raw SQL
     * 
     * @param  string  $startDate  Date in format Y-m-d
     * @param  string  $endDate  Date in format Y-m-d
     * @param  int|null  $userAccountId  Optional filter by user account
     * @param  int|null  $financialAccountId  Optional filter by financial account
     * @param  string|null  $entryType  Optional filter by entry type (debit/credit)
     * @return \Illuminate\Support\Collection
     */
    public static function filterTransactionsByPeriod(
        string $startDate,
        string $endDate,
        ?int $userAccountId = null,
        ?int $financialAccountId = null,
        ?string $entryType = null
    ): \Illuminate\Support\Collection {
        $transactionTable = config('db_tables.transaction', 'transactions');

        // Start with base SQL
        $sql = "SELECT * FROM {$transactionTable} WHERE created_at BETWEEN ? AND ?";
        $bindings = [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ];

        // Add optional filters
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

        // Order by created_at descending
        $sql .= " ORDER BY created_at DESC";

        // Execute raw SQL query
        $results = DB::select($sql, $bindings);

        return collect($results);
    }

    /**
     * Get monthly expenses summary per user within a period
     *
     * Expenses = financial_accounts.type = 'EX'
     *
     * @param  string  $startDate  Y-m-d H:i:s
     * @param  string  $endDate    Y-m-d H:i:s
     * @param  int|null $userId    Optional filter by user ID
     * @return \Illuminate\Support\Collection
     */
    public static function getMonthlyExpensesByUser(
        string $startDate,
        string $endDate,
        ?int $userId = null
    ): \Illuminate\Support\Collection {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $userAccountsTable = config('db_tables.user_account', 'user_accounts');
        $usersTable = config('db_tables.user', 'users');
        $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

        $sql = "
            SELECT 
                u.id AS user_id,
                u.name AS username,
                COALESCE(SUM(t.amount), 0) AS total_expenses
            FROM {$transactionsTable} t
            INNER JOIN {$userAccountsTable} ua ON ua.id = t.user_account_id
            INNER JOIN {$usersTable} u ON u.id = ua.id_user
            INNER JOIN {$financialAccountsTable} fa 
                ON fa.id = t.financial_account_id
            AND fa.type = 'EX'
            WHERE t.created_at >= ?
            AND t.created_at < ?
        ";

        $bindings = [$startDate, $endDate];

        if ($userId !== null) {
            $sql .= " AND ua.id_user = ?";
            $bindings[] = $userId;
        }

        $sql .= "
            GROUP BY u.id, u.name
            ORDER BY total_expenses DESC
        ";

        return collect(DB::select($sql, $bindings));
    }

    /**
     * Filter transaksi dari akun tertentu menggunakan raw SQL
     * 
     * @param  int  $userAccountId  User Account ID
     * @return \Illuminate\Support\Collection
     */
    public static function getTransactionsByUserAccount(int $userAccountId): \Illuminate\Support\Collection
    {
        $query = "
            SELECT
                t.id,
                t.user_account_id,
                t.financial_account_id,
                t.transaction_group_id,
                t.amount,
                t.entry_type,
                t.balance_effect,
                t.is_balance,
                t.description,
                t.created_at,
                t.updated_at,
                ua.username as user_account_username,
                ua.email as user_account_email,
                fa.name as financial_account_name,
                fa.type as financial_account_type
            FROM
                transactions t
            INNER JOIN
                user_accounts ua ON t.user_account_id = ua.id
            INNER JOIN
                financial_accounts fa ON t.financial_account_id = fa.id
            WHERE
                t.user_account_id = ?
            ORDER BY
                t.created_at DESC
        ";

        return collect(DB::select($query, [$userAccountId]));
    }

    public static function getLatestActivitiesRaw()
    {
        $query = "
            SELECT
                t.amount,
                t.description,
                t.created_at,
                t.entry_type, 
                ua.username as user_name,
                a.name as category_name,
                a.type as category_type
            FROM
                transactions t
            JOIN
                user_accounts ua ON t.user_account_id = ua.id
            JOIN
                financial_accounts a ON t.financial_account_id = a.id
            WHERE
                t.created_at >= NOW() - INTERVAL 7 DAY
                AND a.type IN ('IN', 'EX', 'SP')
            ORDER BY
                t.created_at DESC
            LIMIT 20
        ";

        return DB::select($query);
    }
}

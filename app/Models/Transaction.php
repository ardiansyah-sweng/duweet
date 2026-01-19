<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\FinancialAccountColumns;
use App\Enums\AccountType;
use Illuminate\Support\Str;
use App\Constants\UserAccountColumns;
use App\Constants\UserFinancialAccountColumns;
use Carbon\Carbon; // Import Carbon untuk type hinting
use Illuminate\Support\Facades\Schema;

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
    
    // Nama tabel yang sesuai dengan konfigurasi
    protected $table = 'transactions';

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
    ];

    /**
     * Constructor
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = TransactionColumns::getFillable();
    }

    /**
     * Boot method untuk auto-generate transaction_group_id
     */
    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->{TransactionColumns::TRANSACTION_GROUP_ID})) {
                $transaction->{TransactionColumns::TRANSACTION_GROUP_ID} = (string) Str::uuid();
            }
        });
    }


    /**
     * Ambil ringkasan total pendapatan berdasarkan periode (Bulan) untuk user tertentu.
     * Ini adalah implementasi dari query: "sum income user by periode" dengan DML SQL murni.
     *
     * DML SQL (MySQL/MariaDB):
     * -----------------------------------------------------------
     * SELECT 
     *     DATE_FORMAT(t.created_at, '%Y-%m') AS periode,
     *     COALESCE(SUM(t.amount), 0) AS total_income
     * FROM transactions t
     * INNER JOIN financial_accounts fa ON t.financial_account_id = fa.id
     * WHERE t.user_account_id = ?
     *   AND fa.type = 'IN'
     *   AND t.balance_effect = 'increase'
     *   AND fa.is_group = 0
     *   AND t.created_at BETWEEN ? AND ?
     * GROUP BY DATE_FORMAT(t.created_at, '%Y-%m')
     * ORDER BY periode ASC;
     * -----------------------------------------------------------
     *
     * @param int $userAccountId
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        // Gunakan nama tabel dari config bila ada, default ke nama tabel standar
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
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')"; // MySQL/MariaDB
        }

        // Susun DML SQL murni (alias tabel: t, fa)
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

        // Eksekusi raw SQL dengan parameter binding
        $rows = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows);
    }

    /**
        * Ambil ringkasan surplus/defisit dalam rentang tanggal (ADMIN: agregat semua user).
     *
     * Definisi (mengikuti pola query di project ini):
     * - Income  : financial_accounts.type = 'IN' dan transactions.balance_effect = 'increase'
     * - Expense : financial_accounts.type IN ('EX','SP') dan transactions.balance_effect = 'increase'
     *
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getSurplusDeficitSummaryByPeriod(
        Carbon $startDate,
        Carbon $endDate
    ): \Illuminate\Support\Collection {
        $transactionsTable = config('db_tables.transaction');
        $accountsTable = config('db_tables.financial_account');

        $sql = "
            SELECT
                COALESCE(SUM(CASE
                    WHEN fa.type = 'IN' AND t.balance_effect = 'increase' THEN t.amount
                    ELSE 0
                END), 0) AS total_income,
                COALESCE(SUM(CASE
                    WHEN fa.type IN ('EX','SP') AND t.balance_effect = 'increase' THEN t.amount
                    ELSE 0
                END), 0) AS total_expense,
                (
                    COALESCE(SUM(CASE
                        WHEN fa.type = 'IN' AND t.balance_effect = 'increase' THEN t.amount
                        ELSE 0
                    END), 0)
                    -
                    COALESCE(SUM(CASE
                        WHEN fa.type IN ('EX','SP') AND t.balance_effect = 'increase' THEN t.amount
                        ELSE 0
                    END), 0)
                ) AS surplus_deficit
            FROM {$transactionsTable} t
            INNER JOIN {$accountsTable} fa ON fa.id = t.financial_account_id
            WHERE
                fa.is_group = 0
                AND fa.type IN ('IN','EX','SP')
                AND t.created_at BETWEEN ? AND ?
        ";

        $rows = DB::select($sql, [
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

        $transactionTable = config('db_tables.transaction', 'transactions');
        $ids = is_array($userAccountIds) ? $userAccountIds : $userAccountIds->toArray();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $sql = "DELETE FROM {$transactionTable} WHERE user_account_id IN ({$placeholders})";
        
        return DB::delete($sql, $ids);
    }

    /**
     * Hard delete semua transaksi milik user (berdasarkan user_id)
     *
     * @param int $userId
     * @return int
     */ 
    public static function deleteByUserId(int $userId): int
    {
        $userAccountTable = config('db_tables.user_account', 'user_accounts');
        
        $sql = "SELECT id FROM {$userAccountTable} WHERE id_user = ?";
        $userAccountIds = DB::select($sql, [$userId]);
        
        $ids = array_map(fn($row) => $row->id, $userAccountIds);
        
        return self::deleteByUserAccountIds($ids);
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
     * Implementasi dengan DML SQL murni
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
        $transactionTable = config('db_tables.transaction', 'transactions');
        $financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
        $userAccountTable = config('db_tables.user_account', 'user_accounts');

        // Tentukan fungsi format tanggal berdasarkan driver database
        try {
            $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = 'mysql';
        }

        // Define date format based on period dan driver database
        $periodExpr = match($period) {
            'daily' => $driver === 'sqlite' 
                ? "strftime('%Y-%m-%d', t.created_at)"
                : ($driver === 'pgsql' || $driver === 'postgres'
                    ? "to_char(t.created_at, 'YYYY-MM-DD')"
                    : "DATE_FORMAT(t.created_at, '%Y-%m-%d')"),
            'weekly' => $driver === 'sqlite'
                ? "strftime('%Y-W%W', t.created_at)"
                : ($driver === 'pgsql' || $driver === 'postgres'
                    ? "to_char(t.created_at, 'YYYY-IW')"
                    : "DATE_FORMAT(t.created_at, '%Y-W%u')"),
            'yearly' => $driver === 'sqlite'
                ? "strftime('%Y', t.created_at)"
                : ($driver === 'pgsql' || $driver === 'postgres'
                    ? "to_char(t.created_at, 'YYYY')"
                    : "DATE_FORMAT(t.created_at, '%Y')"),
            default => $driver === 'sqlite'
                ? "strftime('%Y-%m', t.created_at)"
                : ($driver === 'pgsql' || $driver === 'postgres'
                    ? "to_char(t.created_at, 'YYYY-MM')"
                    : "DATE_FORMAT(t.created_at, '%Y-%m')"),
        };

        // Build WHERE clause for filtering
        $whereClause = "WHERE fa.type = 'IN' AND t.entry_type = 'credit'";
        $bindings = [];

        if ($startDate) {
            $whereClause .= " AND t.created_at >= ?";
            $bindings[] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $whereClause .= " AND t.created_at <= ?";
            $bindings[] = $endDate . ' 23:59:59';
        }

        if ($userId) {
            $whereClause .= " AND u.id = ?";
            $bindings[] = $userId;
        }

        // Tentukan JOIN clause untuk user filter
        $joinClause = $userId 
            ? "LEFT JOIN {$userAccountTable} AS ua ON t.user_account_id = ua.id 
               LEFT JOIN users AS u ON ua.user_id = u.id"
            : "LEFT JOIN {$userAccountTable} AS ua ON t.user_account_id = ua.id";

        // DML SQL murni
        $sql = "
            SELECT 
                {$periodExpr} AS period,
                SUM(t.amount) AS total_income,
                COUNT(DISTINCT t.transaction_group_id) AS transaction_count,
                COUNT(DISTINCT t.user_account_id) AS user_count
            FROM {$transactionTable} AS t
            JOIN {$financialAccountTable} AS fa ON t.financial_account_id = fa.id
            {$joinClause}
            {$whereClause}
            GROUP BY {$periodExpr}
            ORDER BY period DESC
        ";

        $rows = DB::select($sql, $bindings);

        return collect($rows);
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
        $financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
        $transactionTable = config('db_tables.transaction', 'transactions');
        $userAccountTable = config('db_tables.user_account', 'user_accounts');

        $sql = "
            SELECT 
                fa.id as account_id,
                fa.name as account_name,
                fa.parent_id,
                SUM(t.amount) as total_income,
                COUNT(DISTINCT t.transaction_group_id) as transaction_count
            FROM {$transactionTable} AS t
            INNER JOIN {$financialAccountTable} AS fa ON t.financial_account_id = fa.id
            LEFT JOIN {$userAccountTable} AS ua ON t.user_account_id = ua.id
        ";

        $whereClause = "WHERE fa.type = 'IN' AND t.entry_type = 'credit'";
        $bindings = [];

        if ($startDate) {
            $whereClause .= " AND t.created_at >= ?";
            $bindings[] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $whereClause .= " AND t.created_at <= ?";
            $bindings[] = $endDate . ' 23:59:59';
        }

        if ($userId) {
            $sql .= " INNER JOIN users AS u ON ua.user_id = u.id";
            $whereClause .= " AND u.id = ?";
            $bindings[] = $userId;
        }

        $sql .= " {$whereClause} GROUP BY fa.id, fa.name, fa.parent_id ORDER BY total_income DESC";

        $rows = DB::select($sql, $bindings);

        return collect($rows);
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
        $financialAccountTable = config('db_tables.financial_account', 'financial_accounts');
        $transactionTable = config('db_tables.transaction', 'transactions');
        $userAccountTable = config('db_tables.user_account', 'user_accounts');

        $sql = "
            SELECT 
                COALESCE(SUM(t.amount), 0) as total_income,
                COUNT(DISTINCT t.transaction_group_id) as total_transactions,
                COUNT(DISTINCT t.user_account_id) as total_users,
                COALESCE(AVG(t.amount), 0) as average_income,
                COALESCE(MAX(t.amount), 0) as max_income,
                COALESCE(MIN(t.amount), 0) as min_income
            FROM {$transactionTable} AS t
            INNER JOIN {$financialAccountTable} AS fa ON t.financial_account_id = fa.id
            LEFT JOIN {$userAccountTable} AS ua ON t.user_account_id = ua.id
        ";

        $whereClause = "WHERE fa.type = 'IN' AND t.entry_type = 'credit'";
        $bindings = [];

        if ($startDate) {
            $whereClause .= " AND t.created_at >= ?";
            $bindings[] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $whereClause .= " AND t.created_at <= ?";
            $bindings[] = $endDate . ' 23:59:59';
        }

        if ($userId) {
            $sql .= " INNER JOIN users AS u ON ua.user_id = u.id";
            $whereClause .= " AND u.id = ?";
            $bindings[] = $userId;
        }

        $sql .= " {$whereClause}";

        $summary = DB::selectOne($sql, $bindings);

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

    /**
     * Ambil detail transaksi lengkap via JOIN
     */
    public static function getDetailById($id)
    {
        $transactionTable = config('db_tables.transaction', 'transactions');
        $userAccountTable = config('db_tables.user_account', 'user_accounts');
        $financialAccountTable = config('db_tables.financial_account', 'financial_accounts');

        $sql = "
            SELECT 
                t.id as transaction_id,
                t.transaction_group_id,
                t.amount,
                t.entry_type,
                t.balance_effect,
                t.is_balance,
                t.description,
                t.created_at as transaction_date,
                ua.id as user_account_id,
                ua.username as user_account_username,
                ua.email as user_account_email,
                u.id as id_user,
                u.name as user_name,
                fa.id as financial_account_id,
                fa.name as financial_account_name
            FROM {$transactionTable} AS t
            INNER JOIN {$userAccountTable} AS ua ON ua.id = t.user_account_id
            INNER JOIN users AS u ON u.id = ua.id_user
            INNER JOIN {$financialAccountTable} AS fa ON fa.id = t.financial_account_id
            WHERE t.id = ?
        ";

        $result = DB::selectOne($sql, [$id]);

        return $result;
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
     * Scope: Filter transactions by user account
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userAccountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserAccount($query, $userAccountId)
    {
        return $query->where(TransactionColumns::USER_ACCOUNT_ID, $userAccountId);
    }

    /**
     * Scope: Filter transactions by financial account
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $financialAccountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFinancialAccount($query, $financialAccountId)
    {
        return $query->where(TransactionColumns::FINANCIAL_ACCOUNT_ID, $financialAccountId);
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
     * Ambil transaksi berdasarkan user_account_id dengan opsional filter rentang tanggal.
     * Tidak ada filter entry_type (debit/credit).
     */
    public static function getTransactionsByUserAccount(
        int $userAccountId,
        ?string $startDate = null,
        ?string $endDate = null
    ): \Illuminate\Support\Collection {
        $transactionTable = config('db_tables.transaction');

        // Hanya filter berdasarkan user_account_id dan (opsional) rentang tanggal; entry_type diabaikan (tidak ada debit/credit filter)
        $sql = "SELECT * FROM {$transactionTable} WHERE " . TransactionColumns::USER_ACCOUNT_ID . " = ?";
        $bindings = [$userAccountId];

        // Rentang tanggal (butuh start & end)
        if ($startDate !== null && $endDate !== null) {
            $sql .= " AND created_at BETWEEN ? AND ?";
            $bindings[] = $startDate . ' 00:00:00';
            $bindings[] = $endDate . ' 23:59:59';
        }

        $sql .= " ORDER BY created_at DESC";

        return collect(DB::select($sql, $bindings));
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
     * Scope: Filter transactions by entry type
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $entryType  'debit' or 'credit'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEntryType($query, $entryType)
    {
        return $query->where(TransactionColumns::ENTRY_TYPE, $entryType);
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

    public static function getLatestActivitiesRaw(): array
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

    /**
     * ADMIN REPORT
     * Sum total spending per user account in a given period
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getTotalSpendingByUserAccountAdmin(
        Carbon $startDate,
        Carbon $endDate): \Illuminate\Support\Collection
    {
        $sql = "
            SELECT
                ua.id AS user_account_id,
                ua.username,
                COALESCE(SUM(t.amount), 0) AS total_spending
            FROM transactions t
            INNER JOIN financial_accounts fa
                ON fa.id = t.financial_account_id
            INNER JOIN user_accounts ua
                ON ua.id = t.user_account_id
            WHERE
                fa.type = 'SP'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY ua.id, ua.username
            ORDER BY total_spending DESC
        ";

        return collect(DB::select($sql, [
            $startDate->startOfDay(),
            $endDate->endOfDay(),
        ]));
    }

    
    /**
     * Hard delete semua transaksi dalam satu transaction_group_id dan
     * sesuaikan saldo di tabel `user_financial_accounts`.
     *
     * @param string $groupId
     * @return array{success:bool,message:string,deleted_count:int}
     */
    public static function hardDeleteByGroupId(string $groupId): array
    {
        try {
            $deletedCount = 0;

            DB::transaction(function () use ($groupId, &$deletedCount) {
                $transactions = DB::select("SELECT * FROM " . (new self)->getTable() . " WHERE " . TransactionColumns::TRANSACTION_GROUP_ID . " = ?", [$groupId]);

                if (empty($transactions)) {
                    throw new \Exception('No transactions found for group: ' . $groupId);
                }

                foreach ($transactions as $t) {
                    $userAccountId = $t->{TransactionColumns::USER_ACCOUNT_ID};
                    $financialAccountId = $t->{TransactionColumns::FINANCIAL_ACCOUNT_ID};

                    // Ambil dan lock baris user_financial_accounts dengan FOR UPDATE
                    $ufaRows = DB::select(
                        "SELECT * FROM " . UserFinancialAccountColumns::TABLE . " WHERE " . UserFinancialAccountColumns::USER_ACCOUNT_ID . " = ? AND " . UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID . " = ? FOR UPDATE",
                        [$userAccountId, $financialAccountId]
                    );

                    $ufa = $ufaRows[0] ?? null;

                    if ($ufa) {
                        $current = $ufa->{UserFinancialAccountColumns::BALANCE};

                        if ($t->{TransactionColumns::BALANCE_EFFECT} === 'increase') {
                            $new = $current - $t->{TransactionColumns::AMOUNT};
                        } else {
                            $new = $current + $t->{TransactionColumns::AMOUNT};
                        }

                        DB::update(
                            "UPDATE " . UserFinancialAccountColumns::TABLE . " SET " . UserFinancialAccountColumns::BALANCE . " = ? WHERE " . UserFinancialAccountColumns::ID . " = ?",
                            [$new, $ufa->{UserFinancialAccountColumns::ID}]
                        );
                    }

                    // Hapus baris transaksi (hard delete)
                    DB::delete("DELETE FROM " . (new self)->getTable() . " WHERE " . TransactionColumns::ID . " = ?", [$t->{TransactionColumns::ID}]);

                    $deletedCount++;
                }
            });

            return ['success' => true, 'message' => 'Transactions deleted', 'deleted_count' => $deletedCount];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed: ' . $e->getMessage(), 'deleted_count' => 0];
        }
    }
    
            /**
     * Surplus / Defisit user berdasarkan periode
     *
     * Surplus  = total income - total expense
     */
    public static function getSurplusDefisitByPeriod(
        int $userAccountId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

        // Tentukan format periode (MySQL / SQLite / PostgreSQL)
        try {
            $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = 'mysql';
        }

        if ($driver === 'sqlite') {
            $periodeExpr = "strftime('%Y-%m', t.created_at)";
        } elseif ($driver === 'pgsql') {
            $periodeExpr = "to_char(t.created_at, 'YYYY-MM')";
        } else {
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')";
        }

        $sql = "
            SELECT
                {$periodeExpr} AS periode,
                SUM(CASE
                    WHEN fa.type = 'IN' THEN t.amount
                    ELSE 0
                END) AS total_income,
                SUM(CASE 
                    WHEN fa.type = 'EX' THEN t.amount
                    ELSE 0
                END) AS total_expense,
                (
                    SUM(CASE WHEN fa.type = 'IN' THEN t.amount ELSE 0 END)
                    -
                    SUM(CASE WHEN fa.type = 'EX' THEN t.amount ELSE 0 END)
                ) AS surplus_defisit
            FROM {$transactionsTable} t
            JOIN {$financialAccountsTable} fa
                ON fa.id = t.financial_account_id
            WHERE
                t.user_account_id = ?
                AND t.created_at BETWEEN ? AND ?
            GROUP BY {$periodeExpr}
            ORDER BY periode ASC
        ";

        $rows = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows)->toArray();
    }

    /**
     * ADMIN REPORT
     * Sum total cash-in (income) grouped by period (YYYY-MM) across all users
     * using raw SQL.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getTotalCashinByPeriodAdmin(Carbon $startDate, Carbon $endDate)
    {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

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
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')"; // MySQL/MariaDB
        }

        $sql = "
            SELECT
                {$periodeExpr} AS periode,
                COALESCE(SUM(t.amount), 0) AS total_cashin
            FROM {$transactionsTable} t
            INNER JOIN {$financialAccountsTable} fa ON fa.id = t.financial_account_id
            WHERE
                fa.type = 'IN'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY {$periodeExpr}
            ORDER BY periode ASC
        ";

        $rows = DB::select($sql, [
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows);
    }

    /**
     * Insert transaction using raw SQL
     *
     * @param array $data
     * @return int Last inserted ID
     */
    public static function insertTransactionRaw(array $data): int
    {
        // Use provided transaction_date as created/updated timestamps; fall back to now
        $timestamp = isset($data['transaction_date'])
            ? Carbon::parse($data['transaction_date'])->toDateTimeString()
            : Carbon::now()->toDateTimeString();

        $insertQuery = "INSERT INTO transactions 
            (transaction_group_id, user_account_id, financial_account_id, amount, entry_type, balance_effect, is_balance, description, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        DB::insert(
            $insertQuery,
            [
                $data['transaction_group_id'] ?? (string) Str::uuid(),
                $data['user_account_id'],
                $data['financial_account_id'],
                $data['amount'],
                $data['entry_type'],
                $data['balance_effect'],
                $data['is_balance'] ?? false,
                $data['description'] ?? null,
                $timestamp,
                $timestamp
            ]
        );

        return (int) DB::getPdo()->lastInsertId();
    }
}

     /** Spending summary by period */
    public static function getSpendingSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        $transactionsTable = config('db_tables.transaction');
        $accountsTable = config('db_tables.financial_account');

        $dateColumn = TransactionColumns::TRANSACTION_DATE;
        try {
            if (!Schema::hasColumn($transactionsTable, $dateColumn)) {
                $dateColumn = 'created_at';
            }
        } catch (\Exception $e) {
            // Jika pengecekan schema gagal (mis. koneksi), gunakan default
            $dateColumn = TransactionColumns::TRANSACTION_DATE;
        }

        try {
            $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = 'mysql';
        }

        if ($driver === 'sqlite') {
            $periodeExpr = "strftime('%Y-%m', t." . $dateColumn . ")";
        } elseif ($driver === 'pgsql' || $driver === 'postgres') {
            $periodeExpr = "to_char(t." . $dateColumn . ", 'YYYY-MM')";
        } else {
            $periodeExpr = "DATE_FORMAT(t." . $dateColumn . ", '%Y-%m')"; // MySQL/MariaDB
        }

        $sql = "
            SELECT
                {$periodeExpr} AS periode,
                COALESCE(SUM(t.amount), 0) AS total_spending
            FROM {$transactionsTable} t
            INNER JOIN {$accountsTable} fa ON t.financial_account_id = fa.id
            WHERE
                t.user_account_id = ?
                AND fa.type IN ('EX','SP')
                AND fa.is_group = 0
                AND t." . $dateColumn . " BETWEEN ? AND ?
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
}

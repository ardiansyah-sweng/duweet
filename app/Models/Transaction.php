<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;
use App\Constants\UserFinancialAccountColumns;
use Carbon\Carbon; // Import Carbon untuk type hinting

class Transaction extends Model
{
    use HasFactory;
    
    // Nama tabel yang sesuai dengan konfigurasi
    protected $table = 'transactions';

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

    protected $fillable = [];

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
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

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, TransactionColumns::USER_ACCOUNT_ID);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, TransactionColumns::FINANCIAL_ACCOUNT_ID);
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

    /***
     * ADMIN REPORT
     * Sum total spending per user account in a given period
     */
    public static function getTotalSpendingByUserAccountAdmin(
        Carbon $startDate,
        Carbon $endDate)
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
     * Update transaksi existing (amount, description, created_at) dengan raw SQL 
     * dan sesuaikan saldo di user_financial_accounts
     *
     * DML SQL (MySQL/MariaDB):
     * -----------------------------------------------------------
     * -- 1. Ambil data transaksi lama
     * SELECT * FROM transactions WHERE id = ? FOR UPDATE;
     * 
     * -- 2. Rollback saldo lama
     * UPDATE user_financial_accounts 
     * SET balance = balance +/- old_amount 
     * WHERE user_account_id = ? AND financial_account_id = ?;
     * 
     * -- 3. Update transaksi dengan data baru
     * UPDATE transactions 
     * SET amount = ?, description = ?, created_at = ?, updated_at = NOW()
     * WHERE id = ?;
     * 
     * -- 4. Apply saldo baru
     * UPDATE user_financial_accounts 
     * SET balance = balance +/- new_amount 
     * WHERE user_account_id = ? AND financial_account_id = ?;
     * -----------------------------------------------------------
     *
     * @param int $transactionId
     * @param array $data ['amount' => int, 'description' => string, 'created_at' => string]
     * @return array{success:bool,message:string,data:object|null}
     */
    public static function updateTransaction(int $transactionId, array $data): array
    {
        try {
            $updatedTransaction = null;

            DB::transaction(function () use ($transactionId, $data, &$updatedTransaction) {
                // 1. Ambil transaksi lama dengan lock
                $transactionsTable = config('db_tables.transaction', 'transactions');
                $sql = "SELECT * FROM {$transactionsTable} WHERE " . TransactionColumns::ID . " = ? FOR UPDATE";
                $oldTransactions = DB::select($sql, [$transactionId]);

                if (empty($oldTransactions)) {
                    throw new \Exception('Transaction not found: ' . $transactionId);
                }

                $oldTransaction = $oldTransactions[0];
                $userAccountId = $oldTransaction->{TransactionColumns::USER_ACCOUNT_ID};
                $financialAccountId = $oldTransaction->{TransactionColumns::FINANCIAL_ACCOUNT_ID};
                $oldAmount = $oldTransaction->{TransactionColumns::AMOUNT};
                $balanceEffect = $oldTransaction->{TransactionColumns::BALANCE_EFFECT};

                // 2. Rollback saldo lama dari user_financial_accounts
                $ufaTable = UserFinancialAccountColumns::TABLE;
                $ufaSql = "SELECT * FROM {$ufaTable} WHERE " . 
                    UserFinancialAccountColumns::USER_ACCOUNT_ID . " = ? AND " . 
                    UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID . " = ? FOR UPDATE";
                $ufaRows = DB::select($ufaSql, [$userAccountId, $financialAccountId]);

                if (!empty($ufaRows)) {
                    $ufa = $ufaRows[0];
                    $currentBalance = $ufa->{UserFinancialAccountColumns::BALANCE};

                    // Rollback: reverse effect dari amount lama
                    if ($balanceEffect === 'increase') {
                        $rollbackBalance = $currentBalance - $oldAmount;
                    } else { // decrease
                        $rollbackBalance = $currentBalance + $oldAmount;
                    }

                    DB::update(
                        "UPDATE {$ufaTable} SET " . UserFinancialAccountColumns::BALANCE . " = ? WHERE " . 
                        UserFinancialAccountColumns::ID . " = ?",
                        [$rollbackBalance, $ufa->{UserFinancialAccountColumns::ID}]
                    );

                    // 3. Update transaksi dengan data baru
                    $newAmount = $data['amount'] ?? $oldAmount;
                    $newDescription = $data['description'] ?? $oldTransaction->{TransactionColumns::DESCRIPTION};
                    $newCreatedAt = $data['created_at'] ?? $oldTransaction->{TransactionColumns::CREATED_AT};
                    
                    // Pastikan format timestamp yang benar
                    if ($newCreatedAt instanceof Carbon) {
                        $newCreatedAt = $newCreatedAt->toDateTimeString();
                    }

                    $updateSql = "UPDATE {$transactionsTable} SET " . 
                        TransactionColumns::AMOUNT . " = ?, " . 
                        TransactionColumns::DESCRIPTION . " = ?, " . 
                        TransactionColumns::CREATED_AT . " = ?, " . 
                        TransactionColumns::UPDATED_AT . " = NOW() " . 
                        "WHERE " . TransactionColumns::ID . " = ?";

                    DB::update($updateSql, [
                        $newAmount,
                        $newDescription,
                        $newCreatedAt,
                        $transactionId
                    ]);

                    // 4. Apply saldo baru
                    if ($balanceEffect === 'increase') {
                        $newBalance = $rollbackBalance + $newAmount;
                    } else { // decrease
                        $newBalance = $rollbackBalance - $newAmount;
                    }

                    DB::update(
                        "UPDATE {$ufaTable} SET " . UserFinancialAccountColumns::BALANCE . " = ? WHERE " . 
                        UserFinancialAccountColumns::ID . " = ?",
                        [$newBalance, $ufa->{UserFinancialAccountColumns::ID}]
                    );
                }

                // 5. Ambil data transaksi yang sudah diupdate
                $updatedTransactions = DB::select("SELECT * FROM {$transactionsTable} WHERE " . TransactionColumns::ID . " = ?", [$transactionId]);
                $updatedTransaction = $updatedTransactions[0] ?? null;
            });

            return [
                'success' => true, 
                'message' => 'Transaction updated successfully', 
                'data' => $updatedTransaction
            ];
        } catch (\Exception $e) {
            return [
                'success' => false, 
                'message' => 'Failed to update transaction: ' . $e->getMessage(), 
                'data' => null
            ];
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
}
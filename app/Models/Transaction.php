<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * Disable automatic timestamps if the table doesn't have created_at/updated_at
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Initialize the model with table name from config.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.transaction');
    }

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
     * Get total transactions per user using raw SQL query.
     *
     * Returns essential transaction summary per user:
     * - User information (id, name, email)
     * - transaction_count: Number of transactions (count)
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
        // Get table names from config
        $transactionTable = config('db_tables.transaction');
        $userAccountTable = config('db_tables.user_account');
        $userTable = config('db_tables.user');

        // Get column names from constants
        $userAccountIdCol = TransactionColumns::USER_ACCOUNT_ID;
        $idUserCol = UserAccountColumns::ID_USER;
        $entryTypeCol = TransactionColumns::ENTRY_TYPE;
        $amountCol = TransactionColumns::AMOUNT;

        // Build WHERE clause for filtering by user ID
        $whereClause = '';
        $bindings = [];
        
        if ($userId !== null) {
            $whereClause = "WHERE u.id = ?";
            $bindings[] = $userId;
        }

        // Raw SQL query using DML (Data Manipulation Language)
        $sql = "
            SELECT 
                u.id AS user_id,
                u.name AS user_name,
                u.email AS user_email,
                COALESCE(tx.transaction_count, 0) AS transaction_count,
                COALESCE(tx.debit_count, 0) AS debit_count,
                COALESCE(tx.credit_count, 0) AS credit_count,
                COALESCE(tx.total_debit, 0) AS total_debit,
                COALESCE(tx.total_credit, 0) AS total_credit,
                COALESCE(tx.total_debit, 0) - COALESCE(tx.total_credit, 0) AS net_balance
            FROM {$userTable} AS u
            LEFT JOIN (
                SELECT 
                    ua.{$idUserCol} AS user_id,
                    COUNT(t.id) AS transaction_count,
                    SUM(CASE WHEN t.{$entryTypeCol} = 'debit' THEN 1 ELSE 0 END) AS debit_count,
                    SUM(CASE WHEN t.{$entryTypeCol} = 'credit' THEN 1 ELSE 0 END) AS credit_count,
                    COALESCE(SUM(CASE WHEN t.{$entryTypeCol} = 'debit' THEN t.{$amountCol} ELSE 0 END), 0) AS total_debit,
                    COALESCE(SUM(CASE WHEN t.{$entryTypeCol} = 'credit' THEN t.{$amountCol} ELSE 0 END), 0) AS total_credit
                FROM {$transactionTable} AS t
                JOIN {$userAccountTable} AS ua ON t.{$userAccountIdCol} = ua.id
                GROUP BY ua.{$idUserCol}
            ) AS tx ON tx.user_id = u.id
            {$whereClause}
            ORDER BY net_balance DESC
        ";

        // Execute raw SQL query
        $results = DB::select($sql, $bindings);

        // Convert to collection
        return collect($results);
    }
}

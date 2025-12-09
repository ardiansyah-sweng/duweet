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
            $whereClause = "WHERE ua.id = ?";
            $bindings[] = $userAccountId;
        }

        // Raw SQL query using DML (Data Manipulation Language)
        $sql = "
            SELECT 
                ua.id AS user_account_id,
                ua.email AS user_account_email,
                COALESCE(tx.transaction_count, 0) AS transaction_count
            FROM {$userAccountTable} AS ua
            LEFT JOIN (
                SELECT 
                    t.{$userAccountIdCol} AS user_account_id,
                    COUNT(DISTINCT t.{$transactionGroupIdCol}) AS transaction_count
                FROM {$transactionTable} AS t
                GROUP BY t.{$userAccountIdCol}
            ) AS tx ON ua.id = tx.user_account_id
            {$whereClause}
            ORDER BY transaction_count DESC, ua.id ASC
        ";

        // Execute raw SQL query
        $results = DB::select($sql, $bindings);

        // Convert to collection
        return collect($results);
    }
}

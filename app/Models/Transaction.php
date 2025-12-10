<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.transaction', 'transactions');
    }

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
        TransactionColumns::CREATED_AT => 'datetime',
        TransactionColumns::UPDATED_AT => 'datetime',
    ];

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
}
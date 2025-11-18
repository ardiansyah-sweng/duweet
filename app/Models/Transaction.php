<?php

namespace App\Models;

use App\Constants\TransactionColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.transaction');
    }

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
        TransactionColumns::CREATED_AT => 'datetime',
        TransactionColumns::UPDATED_AT => 'datetime',
    ];

    /**
     * Get the fillable attributes for the model.
     * Uses centralized definition from TransactionColumns constant class.
     *
     * @return array<string>
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
}

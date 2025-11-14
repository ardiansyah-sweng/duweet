<?php

namespace App\Models;

use App\Constants\TransactionColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserAccount;
use App\Models\FinancialAccount;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
        TransactionColumns::CREATED_AT => 'datetime',
        TransactionColumns::UPDATED_AT => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        $this->fillable = TransactionColumns::getFillable();
        parent::__construct($attributes);
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
     * Scope: Filter transaksi berdasarkan periode (date range)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween(TransactionColumns::CREATED_AT, [$startDate, $endDate]);
    }

    /**
     * Scope: Filter transaksi berdasarkan bulan dan tahun
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $month Bulan (1-12)
     * @param int $year Tahun (contoh: 2025)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth(TransactionColumns::CREATED_AT, $month)
                     ->whereYear(TransactionColumns::CREATED_AT, $year);
    }

    /**
     * Scope: Filter transaksi berdasarkan tahun
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $year Tahun (contoh: 2025)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear(TransactionColumns::CREATED_AT, $year);
    }

    /**
     * Scope: Filter transaksi berdasarkan user account
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userAccountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserAccount($query, $userAccountId)
    {
        return $query->where(TransactionColumns::USER_ACCOUNT_ID, $userAccountId);
    }

    /**
     * Scope: Filter transaksi berdasarkan financial account
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $financialAccountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFinancialAccount($query, $financialAccountId)
    {
        return $query->where(TransactionColumns::FINANCIAL_ACCOUNT_ID, $financialAccountId);
    }

    /**
     * Scope: Filter transaksi berdasarkan entry type (debit/credit)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $entryType 'debit' atau 'credit'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEntryType($query, $entryType)
    {
        return $query->where(TransactionColumns::ENTRY_TYPE, $entryType);
    }

    /**
     * Scope: Filter transaksi berdasarkan transaction group ID
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $transactionGroupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTransactionGroup($query, $transactionGroupId)
    {
        return $query->where(TransactionColumns::TRANSACTION_GROUP_ID, $transactionGroupId);
    }
}

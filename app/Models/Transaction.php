<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;
use Carbon\Carbon; // Import Carbon untuk type hinting

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [];

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
        TransactionColumns::CREATED_AT => 'datetime',
        TransactionColumns::UPDATED_AT => 'datetime',
    ];

    protected $hidden = [
        TransactionColumns::CREATED_AT,
        TransactionColumns::UPDATED_AT,
    ];

    protected $appends = ['transaction_date'];

    public function __construct(array $attributes = [])
    {
        $this->fillable = TransactionColumns::getFillable();
        parent::__construct($attributes);
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
     * Scope: Filter transaksi berdasarkan periode (date range)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereDate(TransactionColumns::CREATED_AT, '>=', $startDate)
                     ->whereDate(TransactionColumns::CREATED_AT, '<=', $endDate);
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

    /**
     * Accessor: Alias created_at as transaction_date
     * 
     * @return string|null
     */
    public function getTransactionDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
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
}

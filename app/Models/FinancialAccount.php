<?php

namespace App\Models;

use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];

    protected $casts = [
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

    /**
     * Relasi ke parent account (self-referential)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke children accounts (self-referential)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'financial_account_id');
    }
}

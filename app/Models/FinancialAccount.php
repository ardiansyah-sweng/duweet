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
        'parent_id',
        'name',
        'type',
        'balance',
        'initial_balance',
        'is_group',
        'description',
        'is_active',
        'sort_order',
        'level',
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
        return $this->belongsTo(FinancialAccount::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke children accounts (self-referential)
     */
    public function children(): HasMany
    {
        return $this->hasMany(FinancialAccount::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'financial_account_id');
    }
}

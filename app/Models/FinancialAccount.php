<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Constants\FinancialAccountColumns as C;

class FinancialAccount extends Model
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account');
    }

    protected $fillable = [
        // Use the constants helper from FinancialAccountColumns
        C::NAME,
        C::PARENT_ID,
        C::TYPE,
        C::BALANCE,
        C::INITIAL_BALANCE,
        C::DESCRIPTION,
        C::IS_GROUP,
        C::IS_ACTIVE,
        C::SORT_ORDER,
        C::LEVEL,
    ];

    protected $casts = [
        C::IS_GROUP => 'boolean',
        C::IS_ACTIVE => 'boolean',
        C::BALANCE => 'integer',
        C::INITIAL_BALANCE => 'integer',
        C::SORT_ORDER => 'integer',
        C::LEVEL => 'integer',
    ];

    /**
     * Parent account
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, C::PARENT_ID);
    }

    /**
     * Child accounts
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, C::PARENT_ID);
    }

    /**
     * Users that own/linked to this financial account
     * Assumption: pivot table name is `user_financial_accounts` with columns `user_id` and `financial_account_id`.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_financial_accounts', 'financial_account_id', 'user_id')
            ->withPivot(['balance', 'initial_balance', 'is_active']);
    }

    /**
     * Scope filter by one or multiple types.
     * Accepts string (single) or array/comma-separated string.
     */
    public function scopeOfType($query, $types)
    {
        if (is_string($types)) {
            $types = array_filter(array_map('trim', explode(',', $types)));
        }

        if (empty($types)) {
            return $query;
        }

        return $query->whereIn(C::TYPE, $types);
    }
}

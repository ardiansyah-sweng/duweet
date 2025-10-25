<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\AccountType;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

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

    protected $casts = [
        'type' => AccountType::class,
        'balance' => 'integer',
        'initial_balance' => 'integer',
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get the child accounts.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get the transactions for this account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }
}

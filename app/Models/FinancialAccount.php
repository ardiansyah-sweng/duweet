<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    protected $table = 'financial_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => AccountType::class, // <-- Otomatis cast ke Enum
        'balance' => 'integer',
        'initial_balance' => 'integer',
        'is_group' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'level' => 'integer',
    ];

    /**
     * Get the parent account.
     */
    public function parent()
    {
        return $this->belongsTo(FinancialAccount::class, 'parent_id');
    }

    /**
     * Get the children accounts.
     */
    public function children()
    {
        return $this->hasMany(FinancialAccount::class, 'parent_id');
    }

    /**
     * Get the transactions for this account.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    /**
     * Get the users associated with this financial account.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_financial_accounts', 'financial_account_id', 'user_id')
            ->using(UserFinancialAccount::class)
            ->withPivot('balance', 'initial_balance', 'is_active')
            ->withTimestamps();
    }
}

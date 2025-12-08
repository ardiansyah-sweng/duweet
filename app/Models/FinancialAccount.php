<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Dari branch query_join_user_dgn_transaction_dinar-188
use App\Enums\AccountType;

// Dari branch main
use App\Constants\FinancialAccountColumns;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserFinancialAccount;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'financial_accounts';

    /**
     * Fillable (diambil dari branch main)
     */
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

    /**
     * Casts (digabung)
     */
    protected $casts = [
        // enum dari branch query_join_user_dgn_transaction_dinar-188
        'type' => AccountType::class,

        // casts dari branch main
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',

        // tambahan dari branch query
        'sort_order' => 'integer',
        'level' => 'integer',
    ];

    /**
     * Parent account (digabung)
     */
    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Children accounts (hanya ada di branch query — dipertahankan)
     */
    public function children()
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Transactions relationship (branch query)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    /**
     * Users relationship (branch query)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_financial_accounts', 'financial_account_id', 'user_id')
            ->using(UserFinancialAccount::class)
            ->withPivot('balance', 'initial_balance', 'is_active')
            ->withTimestamps();
    }
}

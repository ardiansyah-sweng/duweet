<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AccountType;                // dari branch dinar-188
use App\Constants\FinancialAccountColumns;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'financial_accounts';

    /**
     * Fillable fields (menggabungkan dari branch main + enum branch)
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
     * Casting (menggabungkan enum cast + boolean cast dari kedua branch)
     */
    protected $casts = [
        FinancialAccountColumns::TYPE => AccountType::class,   // enum cast
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
        FinancialAccountColumns::SORT_ORDER => 'integer',
        FinancialAccountColumns::LEVEL => 'integer',
    ];

    /**
     * Relasi Parent
     */
    public function parent()
    {
        return $this->belongsTo(
            self::class,
            FinancialAccountColumns::PARENT_ID
        );
    }

    /**
     * Relasi Children
     */
    public function children()
    {
        return $this->hasMany(
            self::class,
            FinancialAccountColumns::PARENT_ID
        );
    }

    /**
     * Relasi Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    /**
     * Relasi Users many-to-many (dari branch dinar-188)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_financial_accounts', 'financial_account_id', 'user_id')
            ->using(UserFinancialAccount::class)
            ->withPivot('balance', 'initial_balance', 'is_active')
            ->withTimestamps();
    }
}

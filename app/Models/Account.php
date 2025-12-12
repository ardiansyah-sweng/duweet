<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\AccountColumns;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = AccountColumns::getFillable();

    protected $casts = [
        AccountColumns::IS_GROUP => 'boolean',
        AccountColumns::IS_ACTIVE => 'boolean',
        AccountColumns::BALANCE => 'integer',
        AccountColumns::INITIAL_BALANCE => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, AccountColumns::PARENT_ID);
    }

    public function children()
    {
        return $this->hasMany(Account::class, AccountColumns::PARENT_ID);
    }

    public function transactions()
    {
        return $this->hasMany(Transaksi::class, 'account_id');
    }

    /**
     * Recompute this account's balance from children sums.
     * Only used for group accounts.
     */
    public function recomputeBalanceFromChildren(): void
    {
        $sum = $this->children()->sum('balance');
        $this->balance = $sum;
        $this->save();
    }
}

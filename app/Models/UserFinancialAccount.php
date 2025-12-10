<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';

    protected $fillable = [
        'user_account_id',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'integer',
        'initial_balance' => 'integer',
        'is_active' => 'boolean',
    ];

    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }

    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }
}
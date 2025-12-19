<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';

    protected $fillable = [
        'id_user',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke FinancialAccount
     */
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }
}
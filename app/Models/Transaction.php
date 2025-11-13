<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'transaction_group_id',
        'user_account_id',
        'financial_account_id',
        'entry_type',
        'amount',
        'balance_effect',
        'description',
        'is_balance',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_balance' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_group_id)) {
                $transaction->transaction_group_id = (string) Str::uuid();
            }
        });
    }

    // Relasi ke UserAccount
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }

    // Relasi ke FinancialAccount
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'transaction_group_id',
        'user_id',
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
        'entry_type' => 'string',
        'balance_effect' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // uses user_id
    }

    // Relasi yang benar ke financial_account_id
    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    // Opsional: alias jika kode lama masih memanggil $tx->account
    public function account(): BelongsTo
    {
        return $this->financialAccount();
    }
}
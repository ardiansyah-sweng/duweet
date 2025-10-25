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
        'account_id',
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

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account that owns the transaction.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

<?php

namespace App\Models;

// Pastikan semua 'use' ini ada
use App\Enums\TransactionBalanceEffect;
use App\Enums\TransactionEntryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Pastikan 'use HasFactory' ini ada
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * INI BAGIAN TERPENTING
     * INI YANG MENYEBABKAN ERROR JIKA TIDAK ADA
     */
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

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'entry_type' => TransactionEntryType::class,
        'balance_effect' => TransactionBalanceEffect::class,
        'amount' => 'integer',
        'is_balance' => 'boolean',
    ];

    // --- Relasi ---
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }
}
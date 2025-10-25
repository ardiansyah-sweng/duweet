<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Nama tabel (opsional jika sesuai konvensi Laravel)
     */
    protected $table = 'transactions';

    /**
     * Kolom yang bisa diisi secara mass-assignment
     */
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

    /**
     * Casting otomatis tipe data
     */
    protected $casts = [
        'is_balance' => 'boolean',
        'amount' => 'integer',
    ];

    /**
     * Event boot untuk generate UUID otomatis pada transaction_group_id
     */
    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_group_id)) {
                $transaction->transaction_group_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model FinancialAccount
     */
    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

}

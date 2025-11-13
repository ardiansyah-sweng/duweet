<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserAccount;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';


    protected $fillable = [
        'user_account_id', // Menghubungkan ke tabel user_accounts
        'amount',
        'description',
        'transaction_date', //  Untuk filter "by period"
        'category_id',
    ];

    protected $casts = [
        'amount' => 'float', 
        'transaction_date' => 'datetime',
    ];

    /**
     * Relasi: Satu Transaksi PASTI dimiliki oleh satu UserAccount.
     */
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }
}
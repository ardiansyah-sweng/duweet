<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'description',
        'transaction_date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccountTotal extends Model
{
    protected $table = 'user_account_totals';

    protected $fillable = [
        'user_id', 'account_id', 'total_balance', 'initial_balance'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id');
    }
}

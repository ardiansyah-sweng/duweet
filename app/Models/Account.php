<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'parent_id', 'name', 'type', 'balance', 'initial_balance', 'is_group', 'description', 'is_active', 'sort_order', 'level'
    ];

    public function totals()
    {
        return $this->hasMany(\App\Models\UserAccountTotal::class, 'account_id');
    }
}

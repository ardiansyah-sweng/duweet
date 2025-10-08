<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'parent_id',
        'user_id',
        'name',
        'type',
        'is_group',
        'is_active',
        'balance',
        'initial_balance',
        'description',
        'sort_order',
        'level',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

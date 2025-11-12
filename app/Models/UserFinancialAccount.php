<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_account';

    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
    ];

   public function parent()
{
    return $this->belongsTo(UserFinancialAccount::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(UserFinancialAccount::class, 'parent_id');
}

public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}


}

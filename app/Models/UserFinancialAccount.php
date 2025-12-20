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
        'parent_account_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(UserFinancialAccount::class, 'parent_account_id');
    }

    public function children()
    {
        return $this->hasMany(UserFinancialAccount::class, 'parent_account_id');
    }
}

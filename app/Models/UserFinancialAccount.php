<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = ['user_account_id', 'account_number'];

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

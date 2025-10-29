<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    protected $table = 'user_financial_accounts';
    protected $fillable = [
        'user_id','financial_account_id','balance','initial_balance','is_active'
    ];
    public $timestamps = true;
}


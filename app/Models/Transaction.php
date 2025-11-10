<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_financial_account_id', 'type', 'amount', 'created_at'];

    public function financialAccount()
    {
        return $this->belongsTo(UserFinancialAccount::class, 'user_financial_account_id');
    }
}

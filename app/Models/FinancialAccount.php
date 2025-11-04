<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    protected $table = 'financial_accounts';

    protected $fillable = [
        'name', 'type', 'balance', 'initial_balance',
        'is_group', 'description', 'is_active'
    ];

    public $timestamps=true;
}
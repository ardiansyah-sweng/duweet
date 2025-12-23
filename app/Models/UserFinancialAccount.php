<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';

    // The migration defines an `id` primary key, so incrementing remains true.
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'integer',
        'initial_balance' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_financial_accounts';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true; // Karena Anda punya 'id' (PK) di migrasi pivot

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'integer',
        'initial_balance' => 'integer',
        'is_active' => 'boolean',
    ];  
}

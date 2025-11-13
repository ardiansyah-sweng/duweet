<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserAccount;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'financial_accounts';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'account_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getAccountTypeAttribute($value)
    {
        return $this->attributes['type']; 
    }

    public function setAccountTypeAttribute($value)
    {
        $this->attributes['type'] = $value;
    }

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'financial_account_id');
    }
}
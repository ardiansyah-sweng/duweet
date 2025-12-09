<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use App\Models\UserAccount;
=======
use App\Constants\FinancialAccountColumns;
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494

class FinancialAccount extends Model
{
    use HasFactory;

<<<<<<< HEAD
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
=======
    protected $fillable = [
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];

    protected $casts = [
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }
}
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494

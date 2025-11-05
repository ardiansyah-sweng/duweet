<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'financial_accounts'; 

    protected $fillable = [
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
        // Laravel akan otomatis mengurus created_at dan updated_at
    ];

  
    protected $casts = [
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::SORT_ORDER => 'integer',
        FinancialAccountColumns::LEVEL => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(FinancialAccount::class, FinancialAccountColumns::PARENT_ID);
    }
}
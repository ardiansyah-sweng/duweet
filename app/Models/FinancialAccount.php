<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
use HasFactory;

    protected $table = 'financial_accounts';

    protected $fillable = [
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::IS_ACTIVE,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getActiveAccounts()
    {
        $sql = "
            SELECT *
            FROM financial_accounts
            WHERE is_active = 1
        ";

        return DB::select($sql);
    }
}

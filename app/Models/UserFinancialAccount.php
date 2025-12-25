<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';

    protected $fillable = [
        'user_id',
        'user_account_id',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active',
    ];

    public $timestamps = true;

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke FinancialAccount
     */
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    /**
     * Query DML untuk mendapatkan liquid assets semua user
     * 
     * @return array
     */
    public static function getAllUsersLiquidAssetsQuery()
    {
        return \DB::select("
            SELECT 
                ufa.user_account_id,
                SUM(ufa.balance) as total_liquid_assets
            FROM 
                user_financial_accounts ufa
            INNER JOIN 
                financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE 
                fa.is_liquid = 1 
                AND fa.is_active = 1
                AND ufa.is_active = 1
            GROUP BY 
                ufa.user_account_id
            ORDER BY 
                total_liquid_assets DESC
        ");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     * Sum balances of all users grouped by financial account type.
     * Only include financial accounts that are leaf (is_group = false) and active.
     * Returns associative array keyed by type (IN, EX, SP, LI, AS) with integer totals.
     *
     * @return array<string,int>
     */
    public static function sumAllUsersFinancialAccountsByType(): array
    {
        $types = ['IN', 'EX', 'SP', 'LI', 'AS'];

        $sql = <<<'SQL'
            SELECT fa.type, SUM(ufa.balance) AS total_balance
            FROM user_financial_accounts ufa
            JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE fa.is_group = ?
              AND fa.is_active = ?
              AND ufa.is_active = ?
            GROUP BY fa.type
        SQL;

        $rows = DB::select($sql, [0, 1, 1]);

        $result = array_fill_keys($types, 0);
        foreach ($rows as $r) {
            $result[$r->type] = (int) $r->total_balance;
        }

        return $result;
    }

    /**
     * Query DML untuk mendapatkan liquid assets semua user
     * 
     * @return array
     */
    public static function getAllUsersLiquidAssetsQuery($user_account_id = null)
    {
        $query = "
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
                AND fa.type = 'AS'
        ";
        $bindings = [];
        if ($user_account_id !== null) {
            $query .= " AND ufa.user_account_id = ? ";
            $bindings[] = $user_account_id;
        }
        $query .= " GROUP BY ufa.user_account_id ORDER BY total_liquid_assets DESC ";
        return \DB::select($query, $bindings);
    }
}

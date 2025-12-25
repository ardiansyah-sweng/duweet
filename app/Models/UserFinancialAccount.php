<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public $timestamps = true;

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
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
}

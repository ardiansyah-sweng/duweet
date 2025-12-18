<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
// ...existing code...
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

     /**
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /**
     * Disable automatic timestamps because users table does not have created_at/updated_at
     *
     * @var bool
     */

    public $timestamps = false;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos',
        'tanggal_lahir',
        'bulan_lahir',
        'tahun_lahir',
        'usia',
        'password',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * One user can have many user accounts (credentials)
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }
    public function accounts(): HasMany
    {
        return $this->hasMany(UserAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts')
                    ->withPivot(['initial_balance', 'balance', 'is_active'])
                    ->withTimestamps();
    }

    public function totalLiquidAsset(): int
    {
        return $this->financialAccounts()
            ->whereIn('type', ['AS', 'LI'])
            ->sum('user_financial_accounts.balance');
    }

    public function scopeWithTotalLiquidAsset($query)
    {
        return $query->addSelect([
            'total_liquid_asset' => DB::table('user_financial_accounts as ufa')
                ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
                ->whereColumn('ufa.user_id', 'users.id')
                ->whereIn('fa.type', ['AS','LI'])
                ->selectRaw('COALESCE(SUM(ufa.balance),0)')
        ]);
    }

    public function userFinancialAccounts()
    {
        return $this->hasMany(\App\Models\UserFinancialAccount::class, 'user_id');
    }

    /**
     * Get monthly expenses sum per user for a given period
     * @param string $startDate Start date (Y-m-d H:i:s)
     * @param string $endDate End date (Y-m-d H:i:s)
     * @param int|null $userId Optional user ID filter
     * @return array
     */
    public static function getMonthlyExpenses($startDate, $endDate, $userId = null)
    {
        $sql = "
            SELECT 
                u.id as user_id,
                u.name as username,
                SUM(t.amount) as total_expenses
            FROM transactions t
            INNER JOIN user_accounts ua ON ua.id = t.user_account_id
            INNER JOIN users u ON u.id = ua.id_user
            INNER JOIN financial_accounts fa ON fa.id = t.financial_account_id AND fa.type = 'EX'
            WHERE t.created_at >= ?
              AND t.created_at < ?
        ";

        $bindings = [$startDate, $endDate];

        if ($userId !== null) {
            $sql .= " AND ua.id_user = ?";
            $bindings[] = (int) $userId;
        }

        $sql .= "
            GROUP BY u.id, u.name
            ORDER BY total_expenses DESC
        ";

        return DB::select($sql, $bindings);
    }
}

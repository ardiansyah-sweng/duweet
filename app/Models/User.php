<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\UserTelephone;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use App\Constants\UserAccountColumns;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        return $this->hasMany(UserAccount::class, UserAccountColumns::ID_USER);
    }

    /**
     * Setiap user memiliki satu atau beberapa akun keuangan (UserFinancialAccount)
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }

    /**
     * Get the financial accounts associated with this user.
     * Ini adalah relasi Many-to-Many melalui tabel pivot.
     */
    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts', 'user_id', 'financial_account_id')
            ->using(UserFinancialAccount::class)
            ->withPivot('balance', 'initial_balance', 'is_active')
            ->withTimestamps();
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

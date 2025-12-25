<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        return $this->hasMany(UserAccount::class, 'id_user');
    }
    
    public function accounts() {
        return $this->hasMany(UserAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * DML Murni: Query user yang belum setup account (Base Query)
     * WHERE NOT EXISTS (SELECT 1 FROM user_accounts WHERE id_user = user.id)
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    /**
     * DML Murni: Query user yang belum setup account
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function userBelumSetupAccount()
    {
        $all = self::getAllUsersWithAccountStatus();
        return $all->filter(function($user) {
            return $user->setup_account == 0;
        })->values();
    }

    /**
     * DML Murni: Query user yang sudah setup account
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function userSudahSetupAccount()
    {
        $all = self::getAllUsersWithAccountStatus();
        return $all->filter(function($user) {
            return $user->setup_account == 1;
        })->values();
    }

    /**
     * DML Murni: Get all users dengan status setup account
     * LEFT JOIN dengan user_accounts untuk mengetahui status
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getAllUsersWithAccountStatus()
    {
        return DB::table('users')
            ->leftJoin('user_accounts', 'users.id', '=', 'user_accounts.id_user')
            ->select(
                'users.id',
                'users.name as nama',
                'users.email',
                DB::raw('CASE WHEN MAX(user_accounts.id) IS NOT NULL THEN 1 ELSE 0 END as setup_account'),
                DB::raw('CASE WHEN MAX(user_accounts.id) IS NOT NULL THEN "Sudah Setup" ELSE "Belum Setup" END as status_account')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('users.id')
            ->get();
    }

    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts')
                    ->withPivot(['initial_balance', 'balance', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Setiap user memiliki satu atau beberapa akun keuangan (UserFinancialAccount)
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }
}
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
=======
use App\Models\UserAccount;
>>>>>>> 42bc9f3bbf5a55c80294b126bd1d842b97ae94cb

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
<<<<<<< HEAD
        'name','email','password',
        'usia','bulan_lahir','tahun_lahir','tanggal_lahir',
=======
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
>>>>>>> 42bc9f3bbf5a55c80294b126bd1d842b97ae94cb
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
<<<<<<< HEAD
        return [
            'email_verified_at' => 'datetime',
            'tanggal_lahir'     => 'date',
            'password'          => 'hashed',
        ];
=======
        return $this->hasMany(UserAccount::class, 'id_user');
>>>>>>> 42bc9f3bbf5a55c80294b126bd1d842b97ae94cb
    }

    public function accounts() {
        return $this->hasMany(\App\Models\UserAccount::class);
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

    /**
     * Hitung total liquid asset user.
     * Sesuai PRD diasumsikan hanya tipe 'AS' (Asset) yang dianggap liquid
     * (Jika ke depan ingin memasukkan LI, bisa ditambahkan lewat konfigurasi).
     * - Hanya akun yang active
     * - Hanya leaf account (is_group = false)
     */
    public function totalLiquidAsset(): int
    {
        return (int) $this->financialAccounts()
            ->whereIn('financial_accounts.type', ['AS','LI'])
            ->where('financial_accounts.is_group', false)
            ->wherePivot('is_active', true)
            ->sum('user_financial_accounts.balance');
    }

    /**
     * Scope untuk menambahkan kolom agregat total_liquid_asset pada query users.
     */
    public function scopeWithTotalLiquidAsset($query)
    {
        return $query->addSelect([
            'total_liquid_asset' => DB::table('user_financial_accounts as ufa')
                ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
                ->whereColumn('ufa.user_id', 'users.id')
                ->whereIn('fa.type', ['AS','LI'])
                ->where('fa.is_group', false)
                ->where('ufa.is_active', true)
                ->selectRaw('COALESCE(SUM(ufa.balance),0)')
        ]);
    }
}
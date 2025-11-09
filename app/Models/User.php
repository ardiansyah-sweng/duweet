<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    protected $fillable = [
    'name','email','password',
    'usia','bulan_lahir','tanggal_lahir',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
            ->where('financial_accounts.type', 'AS')
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
                ->where('fa.type', 'AS')
                ->where('fa.is_group', false)
                ->where('ufa.is_active', true)
                ->selectRaw('COALESCE(SUM(ufa.balance),0)')
        ]);
    }
}
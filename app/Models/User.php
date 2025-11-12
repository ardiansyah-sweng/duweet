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
     * Hitung total liquid asset user dengan filter opsional.
     * 
     * @param array $options Filter options:
     *   - type: string|array - Account type filter ('AS', 'LI', ['AS','LI'], etc)
     *   - include_inactive: bool - Include inactive accounts (default: false)
     *   - min_balance: int|float - Minimum balance filter
     * @return int Total liquid asset
     */
    public function totalLiquidAsset(array $options = []): int
    {
        $query = $this->financialAccounts()
            ->where('financial_accounts.is_group', false);

        // Filter by type (default: AS + LI)
        $type = $options['type'] ?? ['AS', 'LI'];
        if (is_string($type)) {
            $query->where('financial_accounts.type', $type);
        } else {
            $query->whereIn('financial_accounts.type', $type);
        }

        // Filter by active status (default: active only)
        if (empty($options['include_inactive'])) {
            $query->wherePivot('is_active', true);
        }

        // Filter by minimum balance
        if (isset($options['min_balance'])) {
            $query->wherePivot('balance', '>=', $options['min_balance']);
        }

        return (int) ($query->sum('user_financial_accounts.balance') ?? 0);
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
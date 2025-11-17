<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< HEAD
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
=======
use App\Models\UserAccount;
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce

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
    'usia','bulan_lahir','tanggal_lahir',
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
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
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

    public function account()
    {
        return $this->hasOne(\App\Models\UserAccount::class, 'user_id');
    }

    public function userFinancialAccounts()
    {
        return $this->hasMany(\App\Models\UserFinancialAccount::class, 'user_id');
    }

    public function scopeSearchUsers($query, $filters)
{
    $norm = fn($v) => ($v === null || trim((string)$v) === '') ? null : trim((string)$v);

    $q        = $norm($filters['q'] ?? null);
    $name     = $norm($filters['name'] ?? null);
    $username = $norm($filters['username'] ?? null);
    $email    = $norm($filters['email'] ?? null);
    $start    = $norm($filters['start'] ?? null);
    $end      = $norm($filters['end'] ?? null);
    $sortBy   = $norm($filters['sort_by'] ?? 'id');
    $sortDir  = strtolower($norm($filters['sort_dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

    // SELECT selalu pakai alias yang eksplisit
    // Build select list depending on whether user_accounts join will be added
    $select = [
        'users.id',
        'users.name',
        'users.email',
        'users.created_at',
    ];

    // We'll append a username expression later after checking table/column availability
    $query->from('users')->select($select);

    // Join ke user_accounts hanya jika tabel (dan kolom) tersedia
    $hasUA   = Schema::hasTable('user_accounts');
    $hasUser = $hasUA && Schema::hasColumn('user_accounts', 'user_id');
    // Ensure username is considered available only when the join can be made (user_id exists)
    $hasUname= $hasUA && $hasUser && Schema::hasColumn('user_accounts', 'username');

    if ($hasUA && $hasUser) {
        $query->leftJoin('user_accounts as ua', 'ua.user_id', '=', 'users.id');
        // jika kolom username tersedia gunakan COALESCE terhadap ua.username
        if ($hasUname) {
            $query->addSelect(DB::raw('COALESCE(ua.username, "") as username'));
        } else {
            // fallback jadi string kosong
            $query->addSelect(DB::raw('"" as username'));
        }
    } else {
        // Pastikan SELECT tetap aman saat tidak ada join
        $query->addSelect(DB::raw('"" as username'));
    }

    // Keyword global
    if ($q) {
        $qLike = '%' . mb_strtolower($q) . '%';
        $query->where(function ($qq) use ($qLike, $hasUname) {
            $qq->whereRaw('LOWER(users.name) LIKE ?', [$qLike])
               ->orWhereRaw('LOWER(users.email) LIKE ?', [$qLike]);

            if ($hasUname) {
                $qq->orWhereRaw('LOWER(COALESCE(ua.username, "")) LIKE ?', [$qLike]);
            }
        });
    }

    // Filter spesifik
    if ($name) {
        $query->whereRaw('LOWER(users.name) LIKE ?', ['%' . mb_strtolower($name) . '%']);
    }
    if ($email) {
        $query->whereRaw('LOWER(users.email) LIKE ?', ['%' . mb_strtolower($email) . '%']);
    }
    if ($username && $hasUname) {
        $query->whereRaw('LOWER(COALESCE(ua.username, "")) LIKE ?', ['%' . mb_strtolower($username) . '%']);
    }
    if ($start) {
        $query->whereDate('users.created_at', '>=', $start);
    }
    if ($end) {
        $query->whereDate('users.created_at', '<=', $end);
    }

    // Sorting aman
    $sortable = ['id', 'name', 'email', 'created_at'];
    if (!in_array($sortBy, $sortable, true)) {
        $sortBy = 'id';
    }

    return $query->orderBy("users.$sortBy", $sortDir);
}

}
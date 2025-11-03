<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\UserTelephone;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    

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

    /**
     * Get the login accounts for the user.
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id');
    }

    /**
     * Get the financial accounts associated with this user.
     * Ini adalah relasi Many-to-Many melalui tabel pivot.
     */
    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts', 'user_id', 'financial_account_id')
            ->using(UserFinancialAccount::class) // Memberi tahu Laravel untuk menggunakan Pivot Model kustom
            ->withPivot('balance', 'initial_balance', 'is_active') // Ambil data tambahan dari tabel pivot
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
        // define all enum types expected
        $types = ['IN', 'EX', 'SP', 'LI', 'AS'];

        $rows = \DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->where('fa.is_group', false)
            ->where('fa.is_active', true)
            ->where('ufa.is_active', true)
            ->select('fa.type', \DB::raw('SUM(ufa.balance) as total_balance'))
            ->groupBy('fa.type')
            ->get();

        $result = array_fill_keys($types, 0);
        foreach ($rows as $r) {
            $result[$r->type] = (int) $r->total_balance;
        }

        return $result;
    }

    /**
     * Sum balances for this user grouped by financial account type.
     * Same filtering rules as the admin method.
     *
     * @return array<string,int>
     */
    public function sumUserFinancialAccountsByType(): array
    {
        $types = ['IN', 'EX', 'SP', 'LI', 'AS'];

        $rows = \DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->where('fa.is_group', false)
            ->where('fa.is_active', true)
            ->where('ufa.is_active', true)
            ->where('ufa.user_id', $this->id)
            ->select('fa.type', \DB::raw('SUM(ufa.balance) as total_balance'))
            ->groupBy('fa.type')
            ->get();

        $result = array_fill_keys($types, 0);
        foreach ($rows as $r) {
            $result[$r->type] = (int) $r->total_balance;
        }

        return $result;
    }

    

   
    
}
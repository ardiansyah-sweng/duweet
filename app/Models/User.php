<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\UserFinancialAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
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

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    public function userFinancialAccounts()
    {
        return $this->hasManyThrough(
            UserFinancialAccount::class,
            UserAccount::class,
            'id_user',
            'user_account_id'
        );
    }

    public function scopeWithoutAccounts($query)
    {
        return $query
            ->leftJoin('user_accounts as ua', 'ua.id_user', '=', 'users.id')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.user_account_id', '=', 'ua.id')
            ->whereNull('ufa.id')
            ->select('users.*');
    }

    /**
     * Scope untuk mendapatkan user yang belum setup account (belum punya user account)
     */
    public function scopeWithoutUserAccount($query)
    {
        return $query
            ->leftJoin('user_accounts as ua', 'ua.id_user', '=', 'users.id')
            ->whereNull('ua.id')
            ->select('users.*');
    }

    public function scopeWithoutActiveAccounts($query)
    {
        return $query
            ->leftJoin('user_accounts as ua_active', 'ua_active.id_user', '=', 'users.id')
            ->leftJoin('user_financial_accounts as ufa_active', function ($join) {
                $join->on('ufa_active.user_account_id', '=', 'ua_active.id')
                    ->where('ufa_active.is_active', 1);
            })
            ->whereNull('ufa_active.id')
            ->select('users.*');
    }

    public static function getAllUsersWithoutAccounts()
    {
        $users = self::withoutAccounts()
            ->select([
                'users.id',
                DB::raw('name'),
                DB::raw('email'),
                DB::raw('usia'),
                DB::raw('bulan_lahir'),
                DB::raw('tanggal_lahir'),
                
            ])
            ->orderBy('users.id', 'asc')
            ->get();

        $hasAccount = [1, 2, 3, 4, 5];

        foreach ($users as $user) {
            if (!in_array($user->id, $hasAccount)) {
               
                $user->name = null;
                $user->email = null;
                $user->usia = null;
                $user->bulan_lahir = null;
                $user->tanggal_lahir = null;
               
            }
        }

        return $users;
    }

    /**
     * Get semua user yang belum setup account (belum punya user account)
     */
    public static function getUsersWithoutSetupAccount()
    {
        return self::withoutUserAccount()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.usia',
                'users.bulan_lahir',
                'users.tanggal_lahir',
                'users.tahun_lahir'
            ])
            ->orderBy('id', 'asc')
            ->get();
    }


    public static function getAllUsersWithoutActiveAccounts()
    {
        $users = self::withoutActiveAccounts()
            ->select([
                'users.id',
                DB::raw('name'),
                DB::raw('email'),
                DB::raw('usia'),
                DB::raw('bulan_lahir'),
                DB::raw('tanggal_lahir'),
         
            ])
            ->orderBy('users.id', 'asc')
            ->get();

        $hasAccount = [1, 2, 3, 4, 5];

        foreach ($users as $user) {
            if (!in_array($user->id, $hasAccount)) {
                $user->name = null;
                $user->email = null;
                $user->usia = null;
                $user->bulan_lahir = null;
                $user->tanggal_lahir = null;
               
            }
        }

        return $users;
    }
}

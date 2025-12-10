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
        return $query->whereRaw(
            "id NOT IN (SELECT DISTINCT id_user FROM user_accounts WHERE id IN (SELECT user_account_id FROM user_financial_accounts))"
        );
    }

    public function scopeWithoutActiveAccounts($query)
    {
        return $query->whereRaw(
            "id NOT IN (SELECT DISTINCT id_user FROM user_accounts WHERE id IN (SELECT user_account_id FROM user_financial_accounts WHERE is_active = 1))"
        );
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

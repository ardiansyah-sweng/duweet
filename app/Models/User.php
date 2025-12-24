<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Disable timestamps (karena tabel users tidak punya created_at & updated_at)
     */
    public $timestamps = false;

    /**
     * Mass assignable fields
     */
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
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'password' => 'hashed',
    ];


    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id');
    }

    public function telephone()
    {
        return $this->hasOne(UserTelephone::class, 'user_id');
    }


    public static function getDataLogin(): ?object
    {
        $account = Auth::user(); // user_accounts (yang login)

        if (!$account) {
            return null;
        }

        return DB::table('users as u')
            ->join('user_accounts as ua', 'ua.user_id', '=', 'u.id')
            ->leftJoin('user_telephones as ut', 'ut.user_id', '=', 'u.id')
            ->where('ua.id', $account->id)
            ->select([
                'u.id as user_id',
                'u.name',
                'u.first_name',
                'u.middle_name',
                'u.last_name',
                'u.email as user_email',
                'u.tanggal_lahir',
                'u.bulan_lahir',
                'u.tahun_lahir',
                'u.usia',
                'ut.number as telephone_number',
                'ua.id as account_id',
                'ua.username',
                'ua.email as login_email',
                'ua.is_active',
                'ua.email_verified_at',
            ])
            ->first();
    }
}

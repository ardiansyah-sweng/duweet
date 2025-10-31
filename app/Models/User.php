<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        public static function getDataLogin($accountId)
{
    return DB::table('users AS u')
        ->join('user_accounts AS ua', 'ua.user_id', '=', 'u.id')
        ->leftJoin('user_telephones AS ut', 'ut.user_id', '=', 'u.id')
        ->select(
            'u.id AS user_id',
            'u.name',
            'u.first_name',
            'u.middle_name',
            'u.last_name',
            'u.email AS user_email',
            'u.tanggal_lahir',
            'u.bulan_lahir',
            'u.tahun_lahir',
            'u.usia',
            'ut.number AS telephone_number',
            'ua.id AS account_id',
            'ua.username',
            'ua.email AS login_email',
            'ua.is_active',
            'ua.email_verified_at'
        )
        ->where('ua.id', $accountId)
        ->first();
}

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
}


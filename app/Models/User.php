<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

use App\Models\UserAccount;

class User extends Authenticatable
{
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

    public static function getDataLogin(int $accountId): ?object
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
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
}

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

    public static function getAllUsersRaw()
    {
        $query = "
            SELECT
                id,
                CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name,
                email,
                nomor_telepon,
                role,
                is_active,
                created_at
            FROM
                users
            ORDER BY
                first_name ASC
        ";

        return DB::select($query);
    }
}

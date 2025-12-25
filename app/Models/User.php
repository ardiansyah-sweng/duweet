<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

use App\Models\UserAccount;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Disable timestamps (created_at & updated_at)
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'nomor_telepon',
        'role',
        'usia',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationship:
     * One user can have many user accounts
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * Get all users using raw SQL query
     */
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
            FROM users
            ORDER BY first_name ASC
        ";

        return DB::select($query);
    }
}

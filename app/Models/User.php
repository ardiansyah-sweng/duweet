<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; 

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Ambil semua user account + data user
     */
    public static function getUserAccounts(int $userId): array
    {
        $query = "
            SELECT
                ua.id   AS user_account_id,
                u.id    AS user_id,
                u.name  AS username,
                u.email,
                u.email_verified_at AS verified_at,
                ua.is_active
            FROM users u
            INNER JOIN user_accounts ua ON ua.user_id = u.id
            WHERE u.id = ?
            ORDER BY ua.id
        ";

        $results = DB::select($query, [$userId]);

        return array_map(
            /**
             * @param object $row
             */
            function ($row): array {
                return [
                    'user_account_id' => (int) $row->user_account_id,
                    'user_id'         => (int) $row->user_id,
                    'username'        => (string) $row->username,
                    'email'           => (string) $row->email,
                    'verified_at'     => $row->verified_at,
                    'is_active'       => (bool) $row->is_active,
                ];
            },
            $results
        );
    }
}

<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $table = 'user_accounts';



    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get the user profile that owns this login account.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function findUserAccountByIdRaw(int $userAccountId)
    {
        $query = "
            SELECT 
                id_userAccount,
                id_user,
                username,
                email,
                status,
                is_active,
                tanggal_daftar,
                tanggal_update
                -- (Kita sengaja tidak select password demi keamanan)
            FROM 
                user_accounts
            WHERE 
                id_userAccount = ?
            LIMIT 1
        ";

        // Menggunakan DB::selectOne karena kita hanya mencari 1 baris
        return DB::selectOne($query, [$userAccountId]);
    }
    

}
<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    public static function updatePassword($newPassword)
    {
        $userId = Auth::id(); // ambil ID user yang sedang login

        $hashed = bcrypt($newPassword); // ini untuk enkripsi password

        return DB::update('
            UPDATE user_accounts
            SET password = ?, updated_at = NOW()
            WHERE user_id = ?
        ', [$hashed, $userId]);
    }


}
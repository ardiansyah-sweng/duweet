<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Users_account extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    protected $fillable = [
        'id_user',
        'username',
        'email',
        'password',
        'verified_at',
        'is_active'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public static function getAllUserAccounts()
    {
        return DB::table('user_accounts')
            ->join('users', 'user_accounts.id_user', '=', 'users.id')
            ->select('user_accounts.*', 'users.*')
            ->get();
    }
}

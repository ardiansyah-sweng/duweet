<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}

<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $table = 'user_accounts';

    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'is_active',
    ];


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



}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal (mass assignment)
    protected $fillable = [
        'user_id',
        'username',
        'email',    
        'password',
        'email_verified_at',
        'is_active',
    ];

    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

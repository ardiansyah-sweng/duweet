<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; // tambahkan untuk query builder

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo', //tambahan photo
        'preference', //tambahan preference
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preference' => 'array',
        ];
    }

    /**
     * Query perintah edit user
     */
    public static function editUser($id, $data)
    {
        return DB::table('users')
            ->where('id', $id)
            ->update([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'photo' => $data['photo'] ?? null,
                'preference' => isset($data['preference']) ? json_encode($data['preference']) : null,
                'updated_at' => now(),
            ]);
    }
}

<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        ];
    }

    public static function createUserRaw(array $data)
    {
        $checkQuery = "SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1";
        $exists = DB::select($checkQuery, [$data['email'], $data['username']]);
        if (!empty($exists)) {
            return 'Email atau Username sudah digunakan.';
        }
        try {
            $insertQuery = "
                INSERT INTO users 
                    (name, username, email, password, created_at, updated_at) 
                VALUES 
                    (?, ?, ?, ?, ?, ?)
            ";

            $hashedPassword = Hash::make($data['password']);
            $now = now();

            DB::insert($insertQuery, [
                $data['name'],
                $data['username'],
                $data['email'],
                $hashedPassword,
                $now,
                $now
            ]);

            return true; 

        } catch (\Exception $e) {
            return 'Gagal menyimpan user ke database.';
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; 
use App\Models\UserAccount; 

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Disable automatic timestamps because users table does not have created_at/updated_at
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',

        'password',
        'photo',
        'preference',

        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos',
        'tanggal_lahir',
        'bulan_lahir',
        'tahun_lahir',
        'usia',
    ];

    /**
     * Attributes to hide.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting.
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'preference' => 'array',
    ];

    /**
     * Relation to UserAccount
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * Query update user
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

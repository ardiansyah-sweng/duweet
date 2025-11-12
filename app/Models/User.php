<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Relasi: satu user bisa punya banyak UserAccount
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user'); // sesuai migration: id_user
    }

    /**
     * Ambil semua akun user sebagai JSON
     */
    public function getAllAccounts()
    {
        return $this->userAccounts()->get()->toJson();
    }
}

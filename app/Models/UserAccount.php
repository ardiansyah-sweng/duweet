<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    use HasFactory;

    /**
     * Model ini tidak menggunakan created_at dan updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'user_accounts';

    /**
     * Kolom yang boleh diisi mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'id_user',
        'username',
        'email',
        'password',
        'verified_at',
        'is_active',
    ];

    /**
     * Casting tipe data otomatis oleh Eloquent.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model User (many to one).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Cari user berdasarkan email.
     *
     * @param  string  $email
     * @return \App\Models\UserAccount|null
     */

    public static function cariUserByEmail($email)
    {
        return DB::select('SELECT * FROM user_accounts WHERE email = ? LIMIT 1', [$email]);
    }

}

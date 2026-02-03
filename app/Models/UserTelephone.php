<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\UserTelephoneColumns as Columns;

class UserTelephone extends Model
{
    use HasFactory;

    /**
     * Nama tabel diambil dari konfigurasi (agar konsisten dengan proyek lain)
     */
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Pastikan di file config/db_tables.php ada: 'user_telephone' => 'user_telephones'
        $this->table = config('db_tables.user_telephone');
    }

    /**
     * Kolom yang bisa diisi secara mass-assignment
     */
    protected $fillable = [
        Columns::USER_ID,
        Columns::NUMBER,
    ];

    /**
     * Relasi ke model User
     * Setiap nomor telepon dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, Columns::USER_ID);
    }

    /**
     * Casting otomatis tipe data (opsional)
     */
    protected $casts = [
        Columns::USER_ID => 'integer',
        Columns::NUMBER => 'string',
    ];
}
<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    /**
     * Table ini tidak menggunakan created_at/updated_at default Laravel
     */
    public $timestamps = false;

    protected $casts = [
        UserAccountColumns::IS_ACTIVE   => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    public function getKeyName()
    {
        return UserAccountColumns::getPrimaryKey();
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_account_id');
    }

    /**
     * Relasi ke UserFinancialAccounts
     * Setiap UserAccount bisa memiliki beberapa akun keuangan
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id', 'user_id');
    }

    /**
     * Insert UserAccount baru menggunakan MURNI SQL (INSERT INTO)
     * Logika hashing dan default value dilakukan di sini.
     * * @param array $data Data yang sudah divalidasi dari controller
     * @return bool
     */
    public static function insertUserAccountRaw(array $data)
    {
        // 1. Siapkan variabel data dari input array
        // Kita gunakan Constant sebagai key agar tidak typo
        $idUser     = $data[UserAccountColumns::ID_USER];
        $username   = $data[UserAccountColumns::USERNAME];
        $email      = $data[UserAccountColumns::EMAIL];
        
        // 2. Hash Password (enkripsi)
        $password   = Hash::make($data[UserAccountColumns::PASSWORD]);
        
        // 3. Set Default Values
        $verifiedAt = now(); 
        $isActive   = 1; // Boolean true di MySQL/MariaDB disimpan sebagai 1

        // 4. Rakit Query SQL Native (INSERT INTO)
        // Kita gunakan concatenation Constant untuk nama kolom agar dinamis & aman
        $tableName = 'user_accounts'; 
        
        $query = "INSERT INTO $tableName (
                    " . UserAccountColumns::ID_USER . ", 
                    " . UserAccountColumns::USERNAME . ", 
                    " . UserAccountColumns::EMAIL . ", 
                    " . UserAccountColumns::PASSWORD . ", 
                    " . UserAccountColumns::VERIFIED_AT . ", 
                    " . UserAccountColumns::IS_ACTIVE . "
                  ) VALUES (?, ?, ?, ?, ?, ?)";

        
        return DB::insert($query, [
            $idUser, 
            $username, 
            $email, 
            $password, 
            $verifiedAt, 
            $isActive
        ]);
    }

    /**
     * Hapus satu UserAccount berdasarkan ID dengan raw query (DELETE FROM)
     * * @param int $id
     * @return array
     */
    public static function deleteUserAccountRaw($id)
    {
        try {
            // Menggunakan raw query DELETE FROM
            $query = "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            DB::delete($query, [$id]);

            return [
                'success' => true,
                'message' => 'UserAccount berhasil dihapus'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghapus UserAccount: ' . $e->getMessage()
            ];
        }
    }
}
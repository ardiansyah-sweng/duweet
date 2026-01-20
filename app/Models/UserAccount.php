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
     * Model ini tidak menggunakan created_at dan updated_at.
     */
    public $timestamps = false;

    /**
     * Casting otomatis.
     */
    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    /**
     * Hidden fields (password tidak ditampilkan).
     */
    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Fillable (menggunakan constant class).
     */
    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    public function getKeyName()
    {
        return UserAccountColumns::getPrimaryKey();
    }

    /**
     * Relasi ke tabel users.
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
     * RAW DELETE USER ACCOUNT (DML)
     */
    public static function deleteUserAccountRaw($id)
    {
        try {
            // Menggunakan raw query DELETE FROM
            $deleteQuery = "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            DB::delete($deleteQuery, [$id]);

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
    /**
     * DML: Cari user account by ID menggunakan RAW QUERY
     */
    public static function cariUserById($id)
    {
        $query = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
        $result = DB::select($query, [$id]);

        return $result[0] ?? null;
    }

    /**
     * DML: Cari user by email menggunakan RAW QUERY
     */
    public static function cariUserByEmail($email)
    {
        $query = "SELECT * FROM user_accounts WHERE email = ? LIMIT 1";
        $result = DB::select($query, [$email]);

        return $result[0] ?? null;
    }

    /**
     * DML: Reset password by email (RAW UPDATE)
     */
    public static function resetPasswordByEmail($email, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "
            UPDATE user_accounts 
            SET password = ?
            WHERE email = ?
        ";

        return DB::update($query, [$hashed, $email]);
    }

    /**
 * DML: Ambil user yang tidak login dalam periode hari tertentu 
 */
public static function query_user_yang_tidak_login_dalam_periode_tertentu($days)
{
     $sql = "
        SELECT ua.*
        FROM user_accounts ua
        LEFT JOIN user_login ul
            ON ua.id = ul.user_account_id
        WHERE (ul.last_login_at IS NULL
               OR ul.last_login_at < DATE_SUB(NOW(), INTERVAL ? DAY))
          AND ua.is_active = 1
    ";

    return DB::select($sql, [$days]);
}
}
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

    public $timestamps = false;

    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
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
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id', 'user_id');
    }

    /**
     * Insert UserAccount baru menggunakan MURNI SQL (INSERT INTO)
     */
    public static function insertUserAccountRaw(array $data)
    {
        $idUser     = $data[UserAccountColumns::ID_USER];
        $username   = $data[UserAccountColumns::USERNAME];
        $email      = $data[UserAccountColumns::EMAIL];
        
        $password   = Hash::make($data[UserAccountColumns::PASSWORD]);
        
        $verifiedAt = now(); 
        $isActive   = 1; 

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
     * ==========================================
     * UPDATE RAW QUERY <-
     * ==========================================
     */
    public static function updateUserAccountRaw($id, array $data)
    {
        // 1. Cek existence
        $existsQuery = "SELECT 1 FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
        $exists = DB::select($existsQuery, [$id]);

        if (empty($exists)) {
            return ['success' => false, 'message' => 'UserAccount tidak ditemukan'];
        }

        // 2. Cek empty data
        if (empty($data)) {
            $selectQuery = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            $userAccount = DB::selectOne($selectQuery, [$id]);
            return ['success' => true, 'message' => 'Tidak ada data untuk diupdate', 'data' => $userAccount];
        }

        // 3. Build Query
        $setClauses = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            if (in_array($column, UserAccountColumns::getFillable())) {
                $setClauses[] = "`$column` = ?";
                $bindings[] = $value;
            }
        }

        if (empty($setClauses)) {
             $selectQuery = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
             $userAccount = DB::selectOne($selectQuery, [$id]);
             return ['success' => true, 'message' => 'Tidak ada data valid untuk diupdate', 'data' => $userAccount];
        }

        $bindings[] = $id; 
        $setString = implode(', ', $setClauses);

        $query = "UPDATE user_accounts SET $setString WHERE " . UserAccountColumns::ID . " = ?";

        try {
            DB::update($query, $bindings);

            $selectQuery = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            $updatedAccount = DB::selectOne($selectQuery, [$id]);

            return [
                'success' => true,
                'message' => 'UserAccount berhasil diupdate',
                'data' => $updatedAccount
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengupdate UserAccount: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hapus satu UserAccount
     */
    public static function deleteUserAccountRaw($id)
    {
        try {
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
     * DML: Cari user account by ID
     */
    public static function cariUserById($id)
    {
        $query = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
        $result = DB::select($query, [$id]);

        return $result[0] ?? null;
    }

    /**
     * DML: Cari user by email
     */
    public static function cariUserByEmail($email)
    {
        $query = "SELECT * FROM user_accounts WHERE email = ? LIMIT 1";
        $result = DB::select($query, [$email]);

        return $result[0] ?? null;
    }

    /**
     * DML: Reset password
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
     * Login Logic
     */
    public static function cariUserByEmailLogin(string $email, string $password)
    {
        $user = DB::select(
            "SELECT * FROM user_accounts WHERE email = ? LIMIT 1",
            [$email]
        );

        if (!empty($user)) {
            $userData = $user[0];
            if (\Illuminate\Support\Facades\Hash::check($password, $userData->password)) {
                return $userData;
            }
        }

        return null;
    }

    public static function cariUserByUsernameLogin(string $username, string $password)
    {
        $user = DB::select(
            "SELECT * FROM user_accounts WHERE username = ? LIMIT 1",
            [$username]
        );

        if (!empty($user)) {
            $userData = $user[0];
            if (\Illuminate\Support\Facades\Hash::check($password, $userData->password)) {
                return $userData;
            }
        }

        return null;
    }
}
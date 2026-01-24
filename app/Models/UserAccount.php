<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Constants\FinancialAccountColumns;
use App\Constants\UserFinancialAccountColumns;
use Illuminate\Support\Facades\Log;

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
     * DML: Cari user berdasarkan email dan password (LOGIKA FIX)
     */
    public static function cariUserByEmailLogin(string $email, string $password)
    {
        $user = DB::select(
            "SELECT * FROM user_accounts WHERE email = ? LIMIT 1",
            [$email]
        );

        if (!empty($user)) {
            $userData = $user[0];

            // Hash::check untuk membandingkan input dengan bcrypt di DB
            if (\Illuminate\Support\Facades\Hash::check($password, $userData->password)) {
                return $userData;
            }
        }

        return null;
    }

    /**
     * DML: Cari user berdasarkan username dan password (LOGIKA FIX)
     */
    public static function cariUserByUsernameLogin(string $username, string $password)
    {
        $user = DB::select(
            "SELECT * FROM user_accounts WHERE username = ? LIMIT 1",
            [$username]
        );

        // Hash::check
        if (!empty($user)) {
            $userData = $user[0];

            if (\Illuminate\Support\Facades\Hash::check($password, $userData->password)) {
                return $userData;
            }
        }

        return null;
    }

    public static function HitungTotalAccountperUser($userId)
    {
        $query = "
            SELECT 
                u." . UserColumns::ID . " AS user_id,
                u." . UserColumns::NAME . " AS name,
                u." . UserColumns::EMAIL . " AS email,
                u." . UserColumns::FIRST_NAME . " AS first_name,
                u." . UserColumns::MIDDLE_NAME . " AS middle_name,
                u." . UserColumns::LAST_NAME . " AS last_name,
                COUNT(ua." . UserAccountColumns::ID . ") AS total_accounts
            FROM users u
            LEFT JOIN user_accounts ua ON ua." . UserAccountColumns::ID_USER . " = u." . UserColumns::ID . "
            WHERE u." . UserColumns::ID . " = ?
            GROUP BY 
                u." . UserColumns::ID . ",
                u." . UserColumns::NAME . ",
                u." . UserColumns::EMAIL . ",
                u." . UserColumns::FIRST_NAME . ",
                u." . UserColumns::MIDDLE_NAME . ",
                u." . UserColumns::LAST_NAME . "
            LIMIT 1
        ";

        $result = DB::selectOne($query, [$userId]);

        if (!$result) {
            return null;
        }

        return [
            'user' => [
                'id' => (int) $result->user_id,
                'name' => $result->name,
                'email' => $result->email,
                'first_name' => $result->first_name,
                'middle_name' => $result->middle_name,
                'last_name' => $result->last_name,
            ],
            'total_accounts' => (int) $result->total_accounts,
        ];
    }

    public static function GetStructureNestedAccountUser()
    {
        try {
            $query = "
                SELECT
                    u." . UserColumns::ID . " AS user_id,
                    u." . UserColumns::NAME . " AS user_name,
                    u." . UserColumns::EMAIL . " AS user_email,
                    ua." . UserAccountColumns::ID . " AS user_account_id,
                    ua." . UserAccountColumns::USERNAME . " AS username,
                    ua." . UserAccountColumns::EMAIL . " AS user_account_email,
                    ua." . UserAccountColumns::IS_ACTIVE . " AS user_account_is_active,
                    fa." . FinancialAccountColumns::ID . " AS financial_account_id,
                    fa." . FinancialAccountColumns::NAME . " AS financial_account_name,
                    fa." . FinancialAccountColumns::TYPE . " AS financial_account_type,
                    ufa." . UserFinancialAccountColumns::BALANCE . " AS user_financial_balance,
                    fa." . FinancialAccountColumns::IS_ACTIVE . " AS financial_account_is_active
                FROM users u
                LEFT JOIN user_accounts ua ON ua." . UserAccountColumns::ID_USER . " = u." . UserColumns::ID . "
                LEFT JOIN user_financial_accounts ufa ON ufa.user_account_id = ua." . UserAccountColumns::ID . "
                LEFT JOIN financial_accounts fa ON fa." . FinancialAccountColumns::ID . " = ufa.financial_account_id
                WHERE ua." . UserAccountColumns::ID . " IS NOT NULL
                ORDER BY u." . UserColumns::ID . ", ua." . UserAccountColumns::ID . ", fa." . FinancialAccountColumns::ID . "
            ";
            
            $results = DB::select($query);
            
            if (empty($results)) {
                return [];
            }
            
            $users = [];
            
            foreach ($results as $row) {
                $userId = (int) $row->user_id;
                $userAccountId = (int) $row->user_account_id;
                
                // Inisialisasi user jika belum ada
                if (!isset($users[$userId])) {
                    $users[$userId] = [
                        'user_id' => $userId,
                        'user_name' => $row->user_name,
                        'user_email' => $row->user_email,
                        'user_accounts' => [],
                    ];
                }
                
                // Inisialisasi user account jika belum ada
                if (!isset($users[$userId]['user_accounts'][$userAccountId])) {
                    $users[$userId]['user_accounts'][$userAccountId] = [
                        'user_account_id' => $userAccountId,
                        'username' => $row->username,
                        'email' => $row->user_account_email,
                        'is_active' => (bool) $row->user_account_is_active,
                        'financial_accounts' => [],
                    ];
                }
                
                // Tambahkan financial account jika ada (tidak null)
                if ($row->financial_account_id !== null) {
                    $users[$userId]['user_accounts'][$userAccountId]['financial_accounts'][] = [
                        'financial_account_id' => (int) $row->financial_account_id,
                        'name' => $row->financial_account_name,
                        'type' => $row->financial_account_type,
                        'balance' => (float) $row->user_financial_balance,
                        'is_active' => (bool) $row->financial_account_is_active,
                    ];
                }
            }
            
            // Konversi array associative ke indexed array
            foreach ($users as &$user) {
                $user['user_accounts'] = array_values($user['user_accounts']);
            }
            
            return array_values($users);
        } catch (\Exception $e) {
            Log::error('Error in GetStructureNestedAccountUser: ' . $e->getMessage());
            return [];
        }
    }

}

<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;
use App\Models\UserFinancialAccount;
use App\Models\FinancialAccount;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Insert user 
     */
    public static function createUserRaw(array $data)
    {
        if (empty($data['email'])) {
            return 'Email harus diisi.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid.';
        }

        try {
            DB::beginTransaction();
            $now = now()->toDateTimeString();

            $existingUser = DB::selectOne(
                "SELECT id FROM users WHERE email = ?",
                [$data['email']]
            );

            if ($existingUser) {
                DB::rollBack();
                return 'Email sudah digunakan.';
            }

            $usia = null;
            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $carbonDate = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR]);
                $usia = $carbonDate->age;
            }

            DB::insert(
                "INSERT INTO users (name, first_name, middle_name, last_name, email, provinsi, kabupaten, kecamatan, jalan, kode_pos, tanggal_lahir, bulan_lahir, tahun_lahir, usia)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $data[UserColumns::NAME] ?? null,
                    $data[UserColumns::FIRST_NAME] ?? null,
                    $data[UserColumns::MIDDLE_NAME] ?? null,
                    $data[UserColumns::LAST_NAME] ?? null,
                    $data['email'],
                    $data[UserColumns::PROVINSI] ?? null,
                    $data[UserColumns::KABUPATEN] ?? null,
                    $data[UserColumns::KECAMATAN] ?? null,
                    $data[UserColumns::JALAN] ?? null,
                    $data[UserColumns::KODE_POS] ?? null,
                    $data[UserColumns::TANGGAL_LAHIR] ?? null,
                    $data[UserColumns::BULAN_LAHIR] ?? null,
                    $data[UserColumns::TAHUN_LAHIR] ?? null,
                    $usia ?? null,
                ]
            );

            $userId = (int) DB::getPdo()->lastInsertId();

            if (!empty($data['telephones'])) {
                foreach ((array) $data['telephones'] as $telephone) {
                    $trimmed = trim((string) $telephone);
                    if ($trimmed !== '') {
                        DB::insert(
                            "INSERT INTO user_telephones (user_id, number, created_at, updated_at)
                             VALUES (?, ?, ?, ?)",
                            [$userId, $trimmed, $now, $now]
                        );
                    }
                }
            }

            DB::commit();
            return $userId;

        } catch (\Exception $e) {
            DB::rollBack();
            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }

    /**
     * UPDATE user (name, email, password, photo, preference)
     */
    public static function updateUserRaw(int $userId, array $data)
    {
        if (empty($data)) {
            return 'Tidak ada data yang diperbarui.';
        }

        try {
            DB::beginTransaction();

            $fields = [];
            $values = [];

            // Name
            if (array_key_exists(UserColumns::NAME, $data)) {
                $fields[] = UserColumns::NAME . ' = ?';
                $values[] = $data[UserColumns::NAME];
            }

            // Email
            if (array_key_exists(UserColumns::EMAIL, $data)) {
                $fields[] = UserColumns::EMAIL . ' = ?';
                $values[] = $data[UserColumns::EMAIL];
            }

            // Password (hash)
            if (!empty($data['password'])) {
                $fields[] = 'password = ?';
                $values[] = bcrypt($data['password']);
            }

            // Photo
            if (array_key_exists(UserColumns::PHOTO, $data)) {
                $fields[] = UserColumns::PHOTO . ' = ?';
                $values[] = $data[UserColumns::PHOTO];
            }

            // Preference (JSON)
            if (array_key_exists(UserColumns::PREFERENCE, $data)) {
                $fields[] = UserColumns::PREFERENCE . ' = ?';
                $values[] = json_encode($data[UserColumns::PREFERENCE]);
            }

            if (empty($fields)) {
                DB::rollBack();
                return 'Tidak ada field valid untuk diperbarui.';
            }

            $values[] = $userId;

            DB::update(
                "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
                $values
            );

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            return 'Gagal update user: ' . $e->getMessage();
        }
    }

    public function accounts()
    {
        return $this->hasMany(UserAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts')
                    ->withPivot(['initial_balance', 'balance', 'is_active'])
                    ->withTimestamps();
    }

    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }

    public static function getUserAccounts($userId)
    {
        $results = DB::select(
            "SELECT ua.id AS user_account_id, ua.id_user, ua.username, ua.email, ua.verified_at, ua.is_active
             FROM user_accounts ua
             WHERE ua.id_user = ?
             ORDER BY ua.id",
            [$userId]
        );

        return array_map(function ($row) {
            return [
                'user_account_id' => $row->user_account_id,
                'id_user' => $row->id_user,
                'username' => $row->username,
                'email' => $row->email,
                'verified_at' => $row->verified_at,
                'is_active' => (bool) $row->is_active
            ];
        }, $results);
    }
}

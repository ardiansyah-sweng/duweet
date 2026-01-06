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
            $now = now();
            $nowString = $now->toDateTimeString();

            $existingUser = DB::selectOne(
                "SELECT id FROM users WHERE email = ?",
                [$data['email']]
            );

            if ($existingUser) {
                DB::rollBack();
                return 'Email sudah digunakan.';
            }

            // --- LOGIKA ASLI ANDA ---
            $usia = null;
            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $carbonDate = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR]);
                $tanggal = $carbonDate->day;
                $bulan = $carbonDate->month;
                $tahun = $carbonDate->year;
                $usia = $carbonDate->age;
            } else {
                $tanggal = $data[UserColumns::TANGGAL_LAHIR] ?? null;
                $bulan = $data[UserColumns::BULAN_LAHIR] ?? null;
                $tahun = $data[UserColumns::TAHUN_LAHIR] ?? null;
            }
            // ------------------------

            DB::insert(
                "INSERT INTO users (name, first_name, middle_name, last_name, email, provinsi, kabupaten, kecamatan, jalan, kode_pos, tanggal_lahir, bulan_lahir, tahun_lahir, usia, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
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
                    $nowString,
                    $nowString
                ]
            );

            $userId = (int) DB::getPdo()->lastInsertId();

            if (!empty($data['telephones'])) {
                $telephones = is_array($data['telephones']) ? $data['telephones'] : [$data['telephones']];
                foreach ($telephones as $telephone) {
                    $trimmed = trim((string) $telephone);
                    if ($trimmed !== '') {
                        DB::insert(
                            "INSERT INTO user_telephones (user_id, number, created_at, updated_at) VALUES (?, ?, ?, ?)",
                            [$userId, $trimmed, $nowString, $nowString]
                        );
                    }
                }
            }

            DB::commit();
            return $userId;

        } catch (\Exception $e) {
            DB::rollBack();
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), '1062')) {
                return 'Email sudah digunakan.';
            }
            return 'Gagal menyimpan user: ' . $e->getMessage();
        }
    }

    /**
     * Update User Profile (Nama, Email, Password, Photo, Preference)
     */
    public static function updateProfileRaw(int $userId, array $data)
    {
        try {
            DB::beginTransaction();
            $updates = [];
            $params = [];

            if (isset($data[UserColumns::NAME])) {
                $updates[] = "name = ?";
                $params[] = $data[UserColumns::NAME];
            }

            if (isset($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) return 'Format email tidak valid.';
                $existing = DB::selectOne("SELECT id FROM users WHERE email = ? AND id != ?", [$data['email'], $userId]);
                if ($existing) return 'Email sudah digunakan.';
                $updates[] = "email = ?";
                $params[] = $data['email'];
            }

            if (!empty($data['password'])) {
                $updates[] = "password = ?";
                $params[] = bcrypt($data['password']);
            }

            if (isset($data['photo'])) {
                $updates[] = "photo = ?";
                $params[] = $data['photo'];
            }

            if (isset($data['preference'])) {
                $updates[] = "preference = ?";
                $params[] = is_array($data['preference']) ? json_encode($data['preference']) : $data['preference'];
            }

            if (empty($updates)) return 'Tidak ada data yang diubah.';

            $updates[] = "updated_at = ?";
            $params[] = now();
            $params[] = $userId;

            DB::update("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?", $params);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return 'Gagal memperbarui profil: ' . $e->getMessage();
        }
    }

    public function accounts() {
        return $this->hasMany(\App\Models\UserAccount::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
    
    public function financialAccounts() {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts')
                    ->withPivot(['initial_balance', 'balance', 'is_active'])
                    ->withTimestamps();
    }

    public function userFinancialAccounts() {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }
}

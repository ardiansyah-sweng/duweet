<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Constants\UserColumns;
use App\Models\UserAccount;
use App\Models\UserFinancialAccount;
use App\Models\FinancialAccount;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Insert user (RAW)
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
            $nowString = now()->toDateTimeString();

            // Cek email existing
            $existingUser = DB::selectOne(
                "SELECT id FROM users WHERE email = ?",
                [$data['email']]
            );

            if ($existingUser) {
                DB::rollBack();
                return 'Email sudah digunakan.';
            }

            // Hitung usia
            $usia = null;
            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $carbonDate = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR]);
                $usia = $carbonDate->age;
            }

            // Insert user
            DB::insert(
                "INSERT INTO users (
                    name, first_name, middle_name, last_name, email,
                    provinsi, kabupaten, kecamatan, jalan, kode_pos,
                    tanggal_lahir, bulan_lahir, tahun_lahir, usia
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
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
                    $usia,
                ]
            );

            $userId = (int) DB::getPdo()->lastInsertId();

            // Insert telephones
            if (!empty($data['telephones'])) {
                $telephones = is_array($data['telephones'])
                    ? $data['telephones']
                    : [$data['telephones']];

                foreach ($telephones as $telephone) {
                    $trimmed = trim((string) $telephone);
                    if ($trimmed !== '') {
                        DB::insert(
                            "INSERT INTO user_telephones (user_id, number, created_at, updated_at)
                             VALUES (?, ?, ?, ?)",
                            [$userId, $trimmed, $nowString, $nowString]
                        );
                    }
                }
            }

            DB::commit();
            return $userId;

        } catch (\Throwable $e) {
            DB::rollBack();

            if (
                str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), '1062') ||
                str_contains($e->getMessage(), 'unique')
            ) {
                return 'Email sudah digunakan.';
            }

            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }

    /**
     * Relasi user -> user_account
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(UserAccount::class);
    }

    /**
     * Relasi user -> transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi many-to-many financial accounts
     */
    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts')
            ->withPivot(['initial_balance', 'balance', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Relasi user -> user_financial_accounts
     */
    public function userFinancialAccounts(): HasMany
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }
}

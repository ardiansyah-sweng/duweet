<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

            $usia = null;
            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $usia = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR])->age;
            }

            // INSERT user
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
                    $usia,
                ]
            );

            $userId = (int) DB::getPdo()->lastInsertId();

            // INSERT telepon 
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

            // untuk kembalikan data
            $userRow = DB::selectOne(
                "SELECT id, name, first_name, middle_name, last_name, email, provinsi, kabupaten, kecamatan, jalan, kode_pos, tanggal_lahir, bulan_lahir, tahun_lahir, usia
                FROM users WHERE id = ?",
                [$userId]
            );

            $telephonesRows = DB::select(
                "SELECT id, user_id, number, created_at, updated_at FROM user_telephones WHERE user_id = ?",
                [$userId]
            );

            DB::commit();

            return [
                'user_id' => $userId,
                'user' => $userRow ? (array) $userRow : null,
                'telephones' => array_map(static fn($row) => (array) $row, $telephonesRows),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (str_contains($e->getMessage(), 'Duplicate entry') || 
                str_contains($e->getMessage(), '1062') ||
                str_contains($e->getMessage(), 'unique')) {
                return 'Email sudah digunakan.';
            }
            
            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }
}
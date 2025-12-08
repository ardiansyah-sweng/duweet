<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;
use App\Models\UserTelephone;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $casts = [
        UserColumns::TANGGAL_LAHIR => 'date',
        UserColumns::BULAN_LAHIR => 'integer',
        UserColumns::TAHUN_LAHIR => 'integer',
        UserColumns::USIA => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'nama_lengkap',
        'usia_otomatis',
        'alamat_lengkap',
    ];

    /**
     * Get the fillable attributes for the model.
     * Uses centralized definition from UserColumns constant class.
     *
     * @return array<string>
     */
    public function getFillable()
    {
        return UserColumns::getFillable();
    }

    /**
     * Relations
     */
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * Get full name (prefer NAME if set, otherwise compose from parts).
     */
    public function getNamaLengkapAttribute(): ?string
    {
        if ($this->{UserColumns::NAME}) {
            return $this->{UserColumns::NAME};
        }

        $parts = array_filter([
            $this->{UserColumns::FIRST_NAME} ?? null,
            $this->{UserColumns::MIDDLE_NAME} ?? null,
            $this->{UserColumns::LAST_NAME} ?? null,
        ]);

        return $parts ? implode(' ', $parts) : null;
    }

    /**
     * Calculate age automatically from tanggal_lahir or tahun_lahir.
     */
    public function getUsiaOtomatisAttribute(): ?int
    {
        if ($this->{UserColumns::USIA}) {
            return (int) $this->{UserColumns::USIA};
        }

        if ($this->{UserColumns::TANGGAL_LAHIR}) {
            return now()->diffInYears($this->{UserColumns::TANGGAL_LAHIR});
        }

        if ($this->{UserColumns::TAHUN_LAHIR}) {
            return now()->year - (int) $this->{UserColumns::TAHUN_LAHIR};
        }

        return null;
    }

    /**
     * Concatenate address fields into a single string.
     */
    public function getAlamatLengkapAttribute(): ?string
    {
        $parts = array_filter([
            $this->{UserColumns::JALAN} ?? null,
            $this->{UserColumns::KECAMATAN} ?? null,
            $this->{UserColumns::KABUPATEN} ?? null,
            $this->{UserColumns::PROVINSI} ?? null,
            $this->{UserColumns::KODE_POS} ?? null,
        ]);

        return $parts ? implode(', ', $parts) : null;
    }

    /**
     * Update address helper.
     */
    public function updateAlamat(array $data): bool
    {
        return $this->update([
            UserColumns::PROVINSI => $data['provinsi'] ?? null,
            UserColumns::KABUPATEN => $data['kabupaten'] ?? null,
            UserColumns::KECAMATAN => $data['kecamatan'] ?? null,
            UserColumns::JALAN => $data['jalan'] ?? null,
            UserColumns::KODE_POS => $data['kode_pos'] ?? null,
        ]);
    }

    /**
     * Create user using raw query dengan validasi email yang robust
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
            $usia = null;

            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $usia = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR])->age;
            }

            $columns = UserColumns::getAllColumns();
            $values = [];
            
            foreach ($columns as $column) {
                $values[] = $data[$column] ?? null;
            }
            
            // Tambahkan timestamps
            $columns[] = 'created_at';
            $columns[] = 'updated_at';
            $values[] = $now;
            $values[] = $now;

            $placeholders = str_repeat('?, ', count($values) - 1) . '?';
            $insertQuery = "INSERT INTO users (" . implode(', ', $columns) . ") VALUES ($placeholders)";

            DB::insert($insertQuery, $values);

            $userId = DB::getPdo()->lastInsertId();

            if (isset($data['telephones']) && !empty($data['telephones'])) {
                $telephones = is_array($data['telephones']) ? $data['telephones'] : [$data['telephones']];
                
                foreach ($telephones as $telephone) {
                    if (!empty(trim($telephone))) {
                        DB::insert(
                            "INSERT INTO user_telephones (user_id, number, created_at, updated_at) VALUES (?, ?, ?, ?)",
                            [$userId, trim($telephone), $now, $now]
                        );
                    }
                }
            }

            DB::commit();
            return $userId;

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
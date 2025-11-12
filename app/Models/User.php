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

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        UserColumns::NAME,
        UserColumns::FIRST_NAME,
        UserColumns::MIDDLE_NAME,
        UserColumns::LAST_NAME,
        UserColumns::EMAIL,
        UserColumns::PROVINSI,
        UserColumns::KABUPATEN,
        UserColumns::KECAMATAN,
        UserColumns::JALAN,
        UserColumns::KODE_POS,
        UserColumns::TANGGAL_LAHIR,
        UserColumns::BULAN_LAHIR,
        UserColumns::TAHUN_LAHIR,
        UserColumns::USIA,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        UserColumns::TANGGAL_LAHIR => 'date',
        UserColumns::BULAN_LAHIR => 'integer',
        UserColumns::TAHUN_LAHIR => 'integer',
        UserColumns::USIA => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Accessors appended to model arrays.
     *
     * @var array<int,string>
     */
    protected $appends = [
        'nama_lengkap',
        'usia_otomatis',
        'alamat_lengkap',
    ];

    /**
     * Relations
     */
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    /**
     * One user can have many user accounts (credentials)
     */
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
        // Validasi input dasar
        if (empty($data['email'])) {
            return 'Email harus diisi.';
        }

        // Validasi format email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid.';
        }

        try {
            DB::beginTransaction();

            $now = now();
            $usia = null;

            // Hitung usia otomatis jika ada tanggal_lahir
            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $usia = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR])->age;
            }

            $insertQuery = "
                INSERT INTO users 
                    (name, first_name, middle_name, last_name, email, 
                    provinsi, kabupaten, kecamatan, jalan, kode_pos, 
                    tanggal_lahir, bulan_lahir, tahun_lahir, usia,
                    created_at, updated_at) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            DB::insert($insertQuery, [
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
                $now,
                $now
            ]);

            $userId = DB::getPdo()->lastInsertId();

            // Insert nomor telepon jika ada
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
            
            // Tangani specifically duplicate entry error
            if (str_contains($e->getMessage(), 'Duplicate entry') || 
                str_contains($e->getMessage(), '1062') ||
                str_contains($e->getMessage(), 'unique')) {
                return 'Email sudah digunakan.';
            }
            
            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }
}
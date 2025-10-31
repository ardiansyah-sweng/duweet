<?php

namespace App\Models;

use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            UserColumns::TANGGAL_LAHIR => 'date',
            UserColumns::BULAN_LAHIR => 'integer',
            UserColumns::TAHUN_LAHIR => 'integer',
            UserColumns::USIA => 'integer',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['nama_lengkap', 'usia_otomatis', 'alamat_lengkap'];

    /**
     * RELATIONSHIPS
     */

    /**
     * Get all telephones for the user.
     */
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    /**
     * SCOPES
     */

    /**
     * Scope a query to only include users by province.
     */
    public function scopeByProvinsi($query, $provinsi)
    {
        return $query->where(UserColumns::PROVINSI, $provinsi);
    }

    /**
     * Scope a query to only include users by city.
     */
    public function scopeByKabupaten($query, $kabupaten)
    {
        return $query->where(UserColumns::KABUPATEN, $kabupaten);
    }

    /**
     * Scope a query to only include users by district.
     */
    public function scopeByKecamatan($query, $kecamatan)
    {
        return $query->where(UserColumns::KECAMATAN, $kecamatan);
    }

    /**
     * Scope a query to only include adult users.
     */
    public function scopeDewasa($query, $minAge = 17)
    {
        return $query->where(UserColumns::USIA, '>=', $minAge);
    }

    /**
     * Scope a query to order users by name.
     */
    public function scopeUrutBerdasarNama($query, $arah = 'asc')
    {
        return $query->orderBy(UserColumns::NAME, $arah)
                    ->orderBy(UserColumns::FIRST_NAME, $arah)
                    ->orderBy(UserColumns::LAST_NAME, $arah);
    }

    /**
     * ACCESSORS
     */

    /**
     * Get the user's full name.
     */
    public function getNamaLengkapAttribute()
    {
        // Jika name sudah ada, gunakan name
        if ($this->{UserColumns::NAME}) {
            return $this->{UserColumns::NAME};
        }

        // Jika tidak, gabungkan first, middle, last name
        $names = array_filter([
            $this->{UserColumns::FIRST_NAME},
            $this->{UserColumns::MIDDLE_NAME},
            $this->{UserColumns::LAST_NAME}
        ]);

        return implode(' ', $names);
    }

    /**
     * Get the user's age (calculated automatically).
     */
    public function getUsiaOtomatisAttribute()
    {
        // Jika usia sudah dihitung dan disimpan di database
        if (!empty($this->{UserColumns::USIA})) {
            return $this->{UserColumns::USIA};
        }

        // Jika ada tanggal_lahir, hitung otomatis
        if ($this->{UserColumns::TANGGAL_LAHIR}) {
            return now()->diffInYears($this->{UserColumns::TANGGAL_LAHIR});
        }

        // Jika ada komponen tanggal lahir terpisah
        if ($this->{UserColumns::TAHUN_LAHIR}) {
            $tahunSekarang = now()->year;
            return $tahunSekarang - $this->{UserColumns::TAHUN_LAHIR};
        }

        return null;
    }

    /**
     * Get the user's complete address.
     */
    public function getAlamatLengkapAttribute()
    {
        $addressParts = array_filter([
            $this->{UserColumns::JALAN},
            $this->{UserColumns::KECAMATAN},
            $this->{UserColumns::KABUPATEN},
            $this->{UserColumns::PROVINSI},
            $this->{UserColumns::KODE_POS},
        ]);

        return $addressParts ? implode(', ', $addressParts) : null;
    }

    /**
     * Get the user's birth date in formatted string.
     */
    public function getTanggalLahirFormattedAttribute()
    {
        if (!$this->{UserColumns::TANGGAL_LAHIR}) {
            return null;
        }

        return $this->{UserColumns::TANGGAL_LAHIR}->translatedFormat('d F Y');
    }

    /**
     * Get the user's birth place.
     */
    public function getTempatLahirAttribute()
    {
        $tempat = array_filter([
            $this->{UserColumns::KABUPATEN},
            $this->{UserColumns::PROVINSI},
        ]);

        return $tempat ? implode(', ', $tempat) : null;
    }

    /**
     * MUTATORS
     */

    /**
     * Set the user's first name.
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes[UserColumns::FIRST_NAME] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * Set the user's middle name.
     */
    public function setMiddleNameAttribute($value)
    {
        $this->attributes[UserColumns::MIDDLE_NAME] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * Set the user's last name.
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes[UserColumns::LAST_NAME] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * Set the user's email.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes[UserColumns::EMAIL] = strtolower($value);
    }

    /**
     * Set the user's province.
     */
    public function setProvinsiAttribute($value)
    {
        $this->attributes[UserColumns::PROVINSI] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * Set the user's city.
     */
    public function setKabupatenAttribute($value)
    {
        $this->attributes[UserColumns::KABUPATEN] = $value ? ucwords(strtolower($value)) : null;
    }

    /**
     * METHODS
     */

    /**
     * Add a telephone number for the user.
     */
    public function tambahTelepon(string $nomor, string $tipe = 'mobile', bool $utama = false)
    {
        // Jika setting sebagai primary, reset existing primary
        if ($utama) {
            $this->telephones()->update(['is_primary' => false]);
        }

        return $this->telephones()->create([
            'number' => $nomor,
            'type' => $tipe,
            'is_primary' => $utama,
            'status' => 'active'
        ]);
    }

    /**
     * Get all telephone numbers as array.
     */
    public function dapatkanSemuaTelepon(): array
    {
        return $this->telephones()->pluck('number')->toArray();
    }

    /**
     * Get primary telephone number.
     */
    public function dapatkanTeleponUtama()
    {
        return $this->telephones()->where('is_primary', true)->first();
    }

    /**
     * Calculate and update user's age.
     */
    public function hitungUsia(): void
    {
        if ($this->{UserColumns::TANGGAL_LAHIR}) {
            $usia = now()->diffInYears($this->{UserColumns::TANGGAL_LAHIR});
            $this->update([UserColumns::USIA => $usia]);
        }
    }

    /**
     * Check if user is adult.
     */
    public function isDewasa(): bool
    {
        $usia = $this->usia_otomatis;
        return $usia && $usia >= 17;
    }

    /**
     * Get user's initials.
     */
    public function getInisial(): string
    {
        $inisial = '';

        if ($this->{UserColumns::FIRST_NAME}) {
            $inisial .= strtoupper(substr($this->{UserColumns::FIRST_NAME}, 0, 1));
        }

        if ($this->{UserColumns::LAST_NAME}) {
            $inisial .= strtoupper(substr($this->{UserColumns::LAST_NAME}, 0, 1));
        }

        return $inisial ?: strtoupper(substr($this->{UserColumns::EMAIL}, 0, 2));
    }

    /**
     * Update user's address.
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
     * Get user's birth information.
     */
    public function getInfoKelahiran(): array
    {
        return [
            'tanggal_lahir' => $this->{UserColumns::TANGGAL_LAHIR},
            'tanggal_lahir_format' => $this->tanggal_lahir_formatted,
            'bulan_lahir' => $this->{UserColumns::BULAN_LAHIR},
            'tahun_lahir' => $this->{UserColumns::TAHUN_LAHIR},
            'usia' => $this->usia_otomatis,
            'tempat_lahir' => $this->tempat_lahir,
        ];
    }
}
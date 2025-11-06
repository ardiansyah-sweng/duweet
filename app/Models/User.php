<?php

namespace App\Models;

use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
}
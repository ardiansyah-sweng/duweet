<?php

namespace App\Constants;

class UserColumns
{
    
    public const ID_USER       = 'id_user';        // Primary Key
    public const NAMA     = 'nama';      // Nama depan
    public const NAMA_AWAL     = 'nama_awal';      // Nama depan
    public const NAMA_TENGAH   = 'nama_tengah';    // Nama tengah
    public const NAMA_AKHIR    = 'nama_akhir';     // Nama belakang
    public const EMAIL         = 'email';          // Email utama user
    public const NOMOR_TELEPON = 'nomor_telepon';  // Nomor HP pengguna
    public const TANGGAL_LAHIR = 'tanggal_lahir';
    public const JENIS_KELAMIN = 'jenis_kelamin';  // Laki-laki / Perempuan

    
    public const PROVINSI      = 'provinsi';
    public const KABUPATEN     = 'kabupaten';
    public const KECAMATAN     = 'kecamatan';
    public const JALAN         = 'jalan';
    public const KODE_POS      = 'kode_pos';

    // Sistem & status
    public const ROLE          = 'role';           // Peran user (admin, staf, user)
    public const IS_ACTIVE     = 'is_active';      // Status aktif (1=aktif, 0=nonaktif)
    public const CREATED_AT    = 'created_at';     // Tanggal dibuat
    public const UPDATED_AT    = 'updated_at';     // Tanggal terakhir diperbarui

    public static function getFillable(): array
    {
        return [
            self::NAMA,
            self::NAMA_AWAL,
            self::NAMA_TENGAH,
            self::NAMA_AKHIR,
            self::EMAIL,
            self::NOMOR_TELEPON,
            self::TANGGAL_LAHIR,
            self::JENIS_KELAMIN,
            self::PROVINSI,
            self::KABUPATEN,
            self::KECAMATAN,
            self::JALAN,
            self::KODE_POS,
            self::ROLE,
            self::IS_ACTIVE,
        ];
    }
}
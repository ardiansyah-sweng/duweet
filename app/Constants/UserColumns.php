<?php

namespace App\Constants;

class UserColumns
{
    // Kolom USERS (Struktur dari MAIN)
    public const ID               = 'id';             // Primary Key (users)
    public const NAME             = 'name';           // Nama lengkap user
    public const FIRST_NAME       = 'first_name';     // Nama depan (nullable)
    public const MIDDLE_NAME      = 'middle_name';    // Nama tengah (nullable)
    public const LAST_NAME        = 'last_name';      // Nama belakang (nullable)
    public const EMAIL            = 'email';          // Email utama (unique)

    // Kolom Tambahan dari LOKAL (HEAD)
    public const NOMOR_TELEPON    = 'nomor_telepon';  // Nomor HP pengguna
    public const JENIS_KELAMIN    = 'jenis_kelamin';  // Laki-laki / Perempuan

    // Data Alamat (Struktur dari MAIN)
    public const PROVINSI         = 'provinsi';       // Nama provinsi tempat tinggal
    public const KABUPATEN        = 'kabupaten';      // Kabupaten / kota
    public const KECAMATAN        = 'kecamatan';      // Kecamatan
    public const JALAN            = 'jalan';          // Nama jalan
    public const KODE_POS         = 'kode_pos';       // Kode pos wilayah

    // Data Lahir (Struktur dari MAIN)
    public const TANGGAL_LAHIR    = 'tanggal_lahir';  // Hari lahir (integer)
    public const BULAN_LAHIR      = 'bulan_lahir';    // Bulan lahir (integer)
    public const TAHUN_LAHIR      = 'tahun_lahir';    // Tahun lahir (integer)
    public const USIA             = 'usia';           // Umur user (integer)

    // Kolom Sistem Tambahan dari LOKAL (HEAD)
    public const ROLE             = 'role';           // Peran user (admin, staf, user)
    public const IS_ACTIVE        = 'is_active';      // Status aktif (1=aktif, 0=nonaktif)
    public const CREATED_AT       = 'created_at';     // Tanggal dibuat
    public const UPDATED_AT       = 'updated_at';     // Tanggal terakhir diperbarui


    public static function getFillable(): array
    {
        return [
            // Kolom dari MAIN
            self::NAME,
            self::FIRST_NAME,
            self::MIDDLE_NAME,
            self::LAST_NAME,
            self::EMAIL,
            self::PROVINSI,
            self::KABUPATEN,
            self::KECAMATAN,
            self::JALAN,
            self::KODE_POS,
            self::TANGGAL_LAHIR,
            self::BULAN_LAHIR,
            self::TAHUN_LAHIR,
            self::USIA,

            // Kolom dari LOKAL (HEAD)
            self::NOMOR_TELEPON,
            self::JENIS_KELAMIN,
            self::ROLE,
            self::IS_ACTIVE,
        ];
    }

    // Method ini dari MAIN
    public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }
}

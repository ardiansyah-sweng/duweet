<?php

namespace App\Constants;

class UserColumns
{
    // Kolom USERS
    public const ID             = 'id';               // Primary Key (users)
    public const NAME           = 'name';             // Nama lengkap user
    public const FIRST_NAME     = 'first_name';       // Nama depan (nullable)
    public const MIDDLE_NAME    = 'middle_name';      // Nama tengah (nullable)
    public const LAST_NAME      = 'last_name';        // Nama belakang (nullable)
    public const EMAIL          = 'email';            // Email utama (unique)

    // Data Alamat (Users)
    public const PROVINSI       = 'provinsi';         // Nama provinsi tempat tinggal
    public const KABUPATEN      = 'kabupaten';        // Kabupaten / kota
    public const KECAMATAN      = 'kecamatan';        // Kecamatan
    public const JALAN          = 'jalan';            // Nama jalan
    public const KODE_POS       = 'kode_pos';         // Kode pos wilayah

    // Data Lahir (Users)
    public const TANGGAL_LAHIR  = 'tanggal_lahir';    // Hari lahir (integer)
    public const BULAN_LAHIR    = 'bulan_lahir';      // Bulan lahir (integer)
    public const TAHUN_LAHIR    = 'tahun_lahir';      // Tahun lahir (integer)
    public const USIA           = 'usia';             // Umur user (integer)

    public static function getFillable(): array
    {
        return [
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
        ];
    }

        public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }
}

<?php

namespace App\Constants;

class UserColumns
{
    // Kolom USERS sesuai PRD
    public const ID             = 'id';               // Primary Key (users)
    public const NAME           = 'name';             // Nama lengkap user
    public const FIRST_NAME     = 'first_name';       // Nama depan (nullable)
    public const MIDDLE_NAME    = 'middle_name';      // Nama tengah (nullable)
    public const LAST_NAME      = 'last_name';        // Nama belakang (nullable)
    public const EMAIL          = 'email';            // Email utama (unique)

    // Data Lahir (Users) - sesuai PRD
    public const TANGGAL_LAHIR  = 'tanggal_lahir';    // Tanggal lahir (integer 1-31)
    public const BULAN_LAHIR    = 'bulan_lahir';      // Bulan lahir (integer 1-12)
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
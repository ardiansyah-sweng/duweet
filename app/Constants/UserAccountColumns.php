<?php

namespace App\Constants;

class UserAccountColumns
{
    // Primary Key
    public const ID_USERACCOUNT   = 'id_userAccount';

    // Foreign Key ke tabel users
    public const ID_USER          = 'id_user';

    // Data akun
    public const USERNAME         = 'username';
    public const PASSWORD         = 'password';
    public const STATUS           = 'status';

    // Timestamp manajemen akun
    public const TANGGAL_DAFTAR   = 'tanggal_daftar';
    public const TANGGAL_UPDATE   = 'tanggal_update';
    public const TANGGAL_HAPUS    = 'tanggal_hapus';

    /**
     * Kolom yang boleh diisi mass-assignment
     */
    public static function getFillable(): array
    {
        return [
            self::ID_USER,
            self::USERNAME,
            self::PASSWORD,
            self::STATUS,
            self::TANGGAL_DAFTAR,
            self::TANGGAL_UPDATE,
            self::TANGGAL_HAPUS,
        ];
    }
}

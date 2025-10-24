<?php

namespace App\Constants;

class UserAccountColumns
{
    // Identitas dasar
    public const ID_USER_ACCOUNT = 'id_userAccount'; // Primary Key
    public const ID_USER         = 'id_user';        // Foreign Key ke tabel user

    // Informasi login
    public const USERNAME        = 'username';       // Nama pengguna untuk login
    public const PASSWORD        = 'password';       // Kata sandi terenkripsi

    // Status & sistem
    public const STATUS          = 'status';         // Status akun (aktif/nonaktif/dihapus)
    public const TANGGAL_DAFTAR  = 'tanggal_daftar'; // Tanggal pembuatan akun
    public const TANGGAL_UPDATE  = 'tanggal_update'; // Tanggal terakhir pembaruan
    public const TANGGAL_HAPUS   = 'tanggal_hapus';  // Tanggal akun dihapus (soft delete)

    
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

<?php

namespace App\Constants;

class UserAccountColumns
{
    // Identitas dasar
    public const ID_USER_ACCOUNT = 'id_userAccount'; // Primary Key
    public const ID_USER         = 'id_user';        // Foreign Key ke tabel users

    // Informasi akun
    public const USERNAME        = 'username';       // Username unik untuk login
    public const EMAIL           = 'email';          // Email unik untuk login
    public const PASSWORD        = 'password';       // Password terenkripsi (hashed)

    // Verifikasi dan status
    public const EMAIL_VERIFIED_AT = 'email_verified_at'; // Timestamp verifikasi email (nullable)
    public const STATUS            = 'status';            // Status akun (aktif/nonaktif)

    public static function getFillable(): array
    {
        return [
            self::ID_USER,
            self::USERNAME,
            self::EMAIL,
            self::PASSWORD,
            self::EMAIL_VERIFIED_AT,
            self::STATUS,
        ];
    }

    public static function getPrimaryKey(): string
    {
        return self::ID_USER_ACCOUNT;
    }

    public static function getForeignKey(): string
    {
        return self::ID_USER;
    }
}

<?php

namespace App\Constants;

class UserColumns
{
     public const TABLE = 'users';

    public const ID              = 'id';
    public const NAME            = 'name';
    public const FIRST_NAME      = 'first_name';
    public const MIDDLE_NAME     = 'middle_name';
    public const LAST_NAME       = 'last_name';
    public const EMAIL           = 'email';
    public const TANGGAL_LAHIR   = 'tanggal_lahir';
    public const BULAN_LAHIR     = 'bulan_lahir';
    public const TAHUN_LAHIR     = 'tahun_lahir';
    public const USIA            = 'usia';
    public const EMAIL_VERIFIED  = 'email_verified_at';
    public const PASSWORD        = 'password';
    public const REMEMBER_TOKEN  = 'remember_token';
    public const CREATED_AT      = 'created_at';
    public const UPDATED_AT      = 'updated_at';

    public static function getFillable(): array
    {
        return [
            self::FIRST_NAME,
            self::MIDDLE_NAME,
            self::LAST_NAME,
            self::EMAIL,
            self::TANGGAL_LAHIR,
            self::BULAN_LAHIR,
            self::TAHUN_LAHIR,
            self::USIA,
            self::EMAIL_VERIFIED,
            self::PASSWORD,
            self::REMEMBER_TOKEN,
            self::CREATED_AT,
            self::UPDATED_AT
        ];
    }
}
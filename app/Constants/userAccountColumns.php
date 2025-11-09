<?php

namespace App\Constants;

class UserAccountColumns
{
	// Identitas dasar
	public const ID              = 'id'; // Primary Key
	public const ID_USER         = 'id_user';        // Foreign Key ke tabel users

	// Informasi akun
	public const USERNAME        = 'username';       // Username unik untuk login
	public const EMAIL           = 'email';          // Email unik untuk login
	public const PASSWORD        = 'password';       // Password terenkripsi (hashed)

	// Verifikasi dan status
	public const VERIFIED_AT     = 'verified_at'; // Timestamp verifikasi email (nullable)
	public const IS_ACTIVE       = 'is_active';            // Status akun (aktif/nonaktif)

	public static function getFillable(): array
	{
		return [
			self::ID,
			self::ID_USER,
			self::USERNAME,
			self::EMAIL,
			self::PASSWORD,
			self::VERIFIED_AT,
			self::IS_ACTIVE,
		];
	}

	public static function getPrimaryKey(): string
	{
		return self::ID;
	}

	public static function getForeignKey(): string
	{
		return self::ID_USER;
	}
}


<?php

namespace App\Constants;

class TransactionColumns
{
    public const ID               = 'id'; // Primary Key
    public const ID_AKUN          = 'id_akun'; // Foreign Key ke tabel akun.id
    public const TANGGAL          = 'tanggal'; // Tanggal transaksi
    public const KETERANGAN       = 'keterangan'; // Deskripsi transaksi
    public const JUMLAH           = 'jumlah'; // Nominal transaksi
    public const JENIS            = 'jenis'; // "DEBIT" atau "KREDIT"
    public const REFERENSI        = 'referensi'; // Nomor referensi opsional
    public const SALDO_SETELAH    = 'saldo_setelah'; // Saldo setelah transaksi ini
    public const DIBUAT_PADA      = 'dibuat_pada'; // created_at
    public const DIPERBARUI_PADA  = 'diperbarui_pada'; // updated_at

    /**
     * Mendapatkan kolom yang bisa diisi (tidak termasuk id, dibuat_pada, diperbarui_pada)
     */
    public static function getFillable(): array
    {
        return [
            self::ID_AKUN,
            self::TANGGAL,
            self::KETERANGAN,
            self::JUMLAH,
            self::JENIS,
            self::REFERENSI,
            self::SALDO_SETELAH,
        ];
    }
}

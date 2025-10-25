<?php

namespace App\Constants;

class TransactionColumns
{
    public const ID                   = 'id';
    public const DEBIT_ACCOUNT_ID     = 'debit_account_id'; // Akun yang menerima
    public const CREDIT_ACCOUNT_ID    = 'credit_account_id'; // Akun yang memberi
    public const TANGGAL              = 'tanggal';
    public const KETERANGAN           = 'keterangan';
    public const JUMLAH               = 'jumlah';
    public const REFERENSI            = 'referensi';
    public const SALDO_SETELAH        = 'saldo_setelah';
    public const CREATED_AT           = 'created_at';
    public const UPDATED_AT           = 'updated_at';

    /**
     * Kolom yang bisa diisi
     */
    public static function getFillable(): array
    {
        return [
            self::DEBIT_ACCOUNT_ID,
            self::CREDIT_ACCOUNT_ID,
            self::TANGGAL,
            self::KETERANGAN,
            self::JUMLAH,
            self::REFERENSI,
            self::SALDO_SETELAH,
        ];
    }
}

<?php

namespace App\Constants;

class AssetColumns
{
    public const ID        = 'id';           // Primary Key Auto Increment
    public const ACCOUNT_ID   = 'financial_account_id';      // Foreign key ke financial_accounts.id dan account_type.asset
    public const ACQUISITION_DATE = 'acquisition_date'; // Tanggal perolehan aset
    public const SOLD_DATE = 'sold_date'; // Tanggal penjualan aset
    public const BOUGHT_PRICE = 'bought_price'; // Harga perolehan aset
    public const SOLD_PRICE = 'sold_price'; // Harga penjualan aset
    public const BUY_QTY = 'buy_quantity'; // Jumlah pembelian aset
    public const SELL_QTY = 'sell_quantity'; // Jumlah penjualan aset
    public const IS_LIQUID = 'is_liquid'; // Apakah aset ini likuid (misal: kas, tabungan)
    public const IS_PRODUCTIVE = 'is_productive'; // Apakah aset ini produktif (misal: investasi)
    public const MEASUREMENT = 'measurement_unit'; // Satuan pengukuran (misal: lot saham, gram emas, unit properti)
    public const HOLDING_PERIOD = 'holding_period'; // Periode kepemilikan dalam days/months/years
    public const IS_SOLD = 'is_sold'; // Status apakah aset sudah dijual

    public static function getFillable(): array
    {
        return [
            self::ACCOUNT_ID,
            self::ACQUISITION_DATE,
            self::SOLD_DATE,
            self::BOUGHT_PRICE,
            self::SOLD_PRICE,
            self::BUY_QTY,
            self::SELL_QTY,
            self::IS_LIQUID,
            self::IS_PRODUCTIVE,
            self::MEASUREMENT,
            self::HOLDING_PERIOD,
            self::IS_SOLD,
        ];
    }

    public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }
}

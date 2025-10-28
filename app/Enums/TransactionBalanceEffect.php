<?php

namespace App\Enums;

/**
 * Enum untuk efek saldo Transaksi
 */
enum TransactionBalanceEffect: string
{
    case INCREASE = 'increase';
    case DECREASE = 'decrease';
}

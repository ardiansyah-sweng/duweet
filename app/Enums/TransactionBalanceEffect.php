<?php

namespace App\Enums;

enum TransactionBalanceEffect: string
{
    case INCREASE = 'increase';
    case DECREASE = 'decrease';
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;

class Admin extends Model
{
    public static function SumCashOutByPeriod($startDate, $endDate)
    {
        $query = "SELECT 
                    SUM(t." . TransactionColumns::AMOUNT . ") AS total_cash_out
                  FROM transactions t
                  WHERE t." . TransactionColumns::BALANCE_EFFECT . " = 'decrease'
                    AND DATE(t." . TransactionColumns::TRANSACTION_DATE . ") >= ?
                    AND DATE(t." . TransactionColumns::TRANSACTION_DATE . ") <= ?";

        $result = DB::selectOne($query, [$startDate, $endDate]);
        return $result ? $result->total_cash_out : 0;
    }
}
<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TransactionEntryType;   // <-- Import Enum
use App\Enums\TransactionBalanceEffect; // <-- Import Enum

class Transaction extends Model
{
    use HasFactory;

 
    protected $table = 'transactions';


    protected $casts = [
        'entry_type'     => TransactionEntryType::class,
        'balance_effect' => TransactionBalanceEffect::class,
        'amount'         => 'integer',
        'is_balance'     => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function financialAccount()
    {
  
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }
    public static function deleteByGroupIdRaw(string $transactionGroupId)
    {
        $query = "
            DELETE FROM 
                transactions 
            WHERE 
                transaction_group_id = ?
        ";

        return DB::delete($query, [$transactionGroupId]);
    }

}
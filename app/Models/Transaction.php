<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'account_id',
        'user_id',
        'amount',
        'description',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    




    public static function querySumExpensesByPeriod(){
         $query = "
            SELECT 
                SUM(t.amount) AS total_expense
            FROM transactions t
            INNER JOIN accounts a ON t.account_id = a.id
            WHERE a.type = 'EX'
        ";

        $result = DB::select($query);

        // Kembalikan nilainya (kalau null, jadi 0)
        return $result[0]->total_expense ?? 0;
    }



}

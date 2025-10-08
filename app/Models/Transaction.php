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


    public static function getLatestActivitiesRaw(){
        $query = "
            SELECT
                t.amount,
                t.description,
                t.created_at,
                u.name as user_name,
                a.name as account_name,
                a.type
            FROM
                transactions as t
            JOIN
                users as u ON t.user_id = u.id
            JOIN
                accounts as a ON t.account_id = a.id
            ORDER BY
                t.created_at DESC
            LIMIT 20
        ";

        return DB::select($query);
    }
}

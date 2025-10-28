<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserTelephone extends Model
{
    protected $table = 'user_telephones';

    protected $fillable = [
            'user_id',
            'number',
        ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }   

}



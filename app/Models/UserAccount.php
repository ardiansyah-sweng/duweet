<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\UserAccountColumns;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';
    public $timestamps = false;

    protected $casts = [
        UserAccountColumns::IS_ACTIVE   => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    public function getKeyName()
    {
        return UserAccountColumns::getPrimaryKey();
    }
}

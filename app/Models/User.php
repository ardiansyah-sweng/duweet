<?php

namespace App\Models;

use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // assign fillable secara runtime
        $this->fillable = UserColumns::getFillable();
        $this->primaryKey = UserColumns::ID;
    }

    // Relasi ke UserAccount (1 user punya 1 account)
    public function account()
    {
        return $this->hasMany(UserAccount::class, 'id_user', $this->primaryKey);
    }
}

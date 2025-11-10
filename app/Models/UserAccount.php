<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\UserAccountColumns;

class UserAccount extends Model
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.user_account');
    }
}

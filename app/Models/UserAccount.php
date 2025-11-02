<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\UserAccountColumns;
use App\Models\User;

class UserAccount extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'user_accounts';
    protected $fillable = [];
    protected $primaryKey;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // assign fillable dan primary key saat runtime
        $this->fillable = UserAccountColumns::getFillable();
        $this->primaryKey = UserAccountColumns::getPrimaryKey();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}

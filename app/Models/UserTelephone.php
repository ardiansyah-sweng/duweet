<?php

namespace App\Models;

use App\Constants\UserTelephoneColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTelephone extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_telephones';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = UserTelephoneColumns::ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = UserTelephoneColumns::getFillable();

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the telephone.
     */
    public function user()
    {
        return $this->belongsTo(User::class, UserTelephoneColumns::USER_ID);
    }
}
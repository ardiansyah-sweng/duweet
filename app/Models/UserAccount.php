<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    /**
     * This table does not use created_at/updated_at timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $table = 'user_accounts';

    protected $fillable = [
        'id_user',
        'username',
        'email',
        'password',
        'verified_at',
        'is_active',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
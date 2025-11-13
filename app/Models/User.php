<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\UserAccount;
use App\Models\Transaction;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Disable automatic timestamps because users table does not have created_at/updated_at
     *
     * @var bool
     */
    public $timestamps = false;
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos',
        'tanggal_lahir',
        'bulan_lahir',
        'tahun_lahir',
        'usia',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [];

    /**
     * One user can have many user accounts (credentials)
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * HasManyThrough relationship to the transactions table.
     * 
     * This relationship is used by the ReportController
     * to calculate the user's Surplus/Deficit.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class, 
            UserAccount::class,
            'id_user',         // Foreign key in user_accounts table referencing users.id
            'user_account_id', // Foreign key in transactions table referencing user_accounts.id
            'id',              // Local key in users table (primary key)
            'id'               // Local key in user_accounts table (primary key)
        );
    }
}

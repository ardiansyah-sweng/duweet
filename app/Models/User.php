<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\UserAccount;
use App\Models\Transaction;
use App\Models\UserFinancialAccount;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    protected $hidden = [];
    protected $casts = [];

    /**
     * One user can have many user accounts (credentials)
     */
    public function userAccounts(): HasMany
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * HasManyThrough relationship to the transactions table.
     * Used by ReportController to calculate the user's Surplus/Deficit.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class, 
            UserAccount::class,
            'id_user',         // Foreign key in user_accounts referencing users.id
            'user_account_id', // Foreign key in transactions referencing user_accounts.id
            'id',              // Local key in users table
            'id'               // Local key in user_accounts table
        );
    }

    /**
     * One user can have many financial accounts (UserFinancialAccount)
     */
    public function userFinancialAccounts(): HasMany
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }
}

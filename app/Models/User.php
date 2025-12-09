<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\UserTelephone;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Mass assignable attributes
     *
     * @var array<int, string>
     */
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
     * Hidden attributes
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * One user can have many user accounts
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id');
    }

    /**
     * One user can have many phone numbers
     */
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    /**
     * A user can have many transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Many-to-Many relationship with pivot financial accounts
     */
    public function financialAccounts()
    {
        return $this->belongsToMany(
            FinancialAccount::class,
            'user_financial_accounts',
            'user_id',
            'financial_account_id'
        )
        ->using(UserFinancialAccount::class)
        ->withPivot('balance', 'initial_balance', 'is_active')
        ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\UserAccount;
use App\Models\UserTelephone;
use App\Models\Transaction;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Karena tabel users tidak memiliki created_at/updated_at
     */
    public $timestamps = false;

    /**
     * Mass assignable attributes
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
     * Attributes to hide in serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * RELATIONS
     */

    // User memiliki banyak UserAccount
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id');
    }

    // User memiliki banyak nomor telepon
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    // User memiliki banyak transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Relasi Many-to-Many dengan FinancialAccount melalui pivot
     */
    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts', 'user_id', 'financial_account_id')
            ->using(UserFinancialAccount::class)
            ->withPivot('balance', 'initial_balance', 'is_active')
            ->withTimestamps();
    }
}

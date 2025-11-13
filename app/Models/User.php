<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserAccount;
use App\Models\Transaction;

class User extends Authenticatable
{
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * One user can have many user accounts (credentials)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userAccounts(): HasMany
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * User transactions relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
    
    public function deleteTransaction(int $transactionId): bool
    {
        $transaction = $this->transactions()->find($transactionId);

        if (! $transaction) {
            return false;
        }

        return (bool) $transaction->delete();
    }
}

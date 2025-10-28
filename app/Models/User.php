<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\UserTelephone;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'tanggal_lahir',
        'jenis_kelamin',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    /**
     * Get the login accounts for the user.
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id');
    }

    /**
     * Get the transactions recorded by this user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get the financial accounts associated with this user.
     * Ini adalah relasi Many-to-Many melalui tabel pivot.
     */
    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts', 'user_id', 'financial_account_id')
            ->using(UserFinancialAccount::class) // Memberi tahu Laravel untuk menggunakan Pivot Model kustom
            ->withPivot('balance', 'initial_balance', 'is_active') // Ambil data tambahan dari tabel pivot
            ->withTimestamps();
    }

   
    
}

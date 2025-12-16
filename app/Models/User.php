<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Constants\UserColumns;
=======
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;
8edd2f12696008b0f7dd219ec55e5e922

class User extends Model
{
    use HasFactory;


    protected $table = 'users';
    protected $primaryKey = UserColumns::ID;
    protected $fillable = UserColumns::getFillable();

    protected $casts = [
        UserColumns::TANGGAL_LAHIR => 'integer',
        UserColumns::BULAN_LAHIR   => 'integer',
        UserColumns::TAHUN_LAHIR   => 'integer',
        UserColumns::USIA          => 'integer',
    ];

    public function accounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id', 'id');

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * One user can have many user accounts (credentials)
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
>>>>>>> 704974a8edd2f12696008b0f7dd219ec55e5e922
    }

    public function scopeActiveUsers($query)
    {
        return $query->whereHas('accounts', function ($q) {
            $q->where('is_active', true);
        });
    }
     
    public static function getActiveUsers()
    {
        return self::with(['accounts' => function ($q) {
            $q->where('is_active', true)
              ->select('id', 'user_id', 'username', 'email', 'is_active');
        }])
        ->whereHas('accounts', function ($q) {
            $q->where('is_active', true);
        })
        ->orderBy(UserColumns::NAME)
        ->get([
            UserColumns::ID,
            UserColumns::NAME,
            UserColumns::EMAIL,
        ]);
    }
}
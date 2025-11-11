<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\UserColumns;

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
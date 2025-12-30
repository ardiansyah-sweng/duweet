<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

use App\Models\UserAccount;
use App\Models\UserFinancialAccount;
use App\Models\FinancialAccount;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Disable timestamps (created_at & updated_at)
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'nomor_telepon',
        'role',
        'usia',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
    protected $table = 'users';
    

    /**
     * Insert user 
     */
    public static function createUserRaw(array $data)
    {
        if (empty($data['email'])) {
            return 'Email harus diisi.';
        }

    /**
     * Relationship:
     * One user can have many user accounts
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }
        // Pindahkan validasi email ke sini
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid.';
        }

        try {
            // memulai transaksi database
            DB::beginTransaction();
            $now = now();
            $nowString = $now->toDateTimeString();

            // Validasi email jika sudah ada
            $existingUser = DB::selectOne(
                "SELECT id FROM users WHERE email = ?",
                [$data['email']]
            );

            if ($existingUser) {
                DB::rollBack();
                return 'Email sudah digunakan.';
            }

            // Menghitung usia
            $usia = null;
            if (!empty($data[UserColumns::TANGGAL_LAHIR])) {
                $carbonDate = \Carbon\Carbon::parse($data[UserColumns::TANGGAL_LAHIR]);
                $tanggal = $carbonDate->day;
                $bulan = $carbonDate->month;
                $tahun = $carbonDate->year;
                $usia = $carbonDate->age;
            } else {
                // Jika terpisah, ambil langsung dari data
                $tanggal = $data[UserColumns::TANGGAL_LAHIR] ?? null;
                $bulan = $data[UserColumns::BULAN_LAHIR] ?? null;
                $tahun = $data[UserColumns::TAHUN_LAHIR] ?? null;
            }

            // INSERT user - Perbaikan: jumlah placeholder (14) harus sama dengan jumlah value
            DB::insert(
                "INSERT INTO users (name, first_name, middle_name, last_name, email, provinsi, kabupaten, kecamatan, jalan, kode_pos, tanggal_lahir, bulan_lahir, tahun_lahir, usia)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $data[UserColumns::NAME] ?? null,
                    $data[UserColumns::FIRST_NAME] ?? null,
                    $data[UserColumns::MIDDLE_NAME] ?? null,
                    $data[UserColumns::LAST_NAME] ?? null,
                    $data['email'],
                    $data[UserColumns::PROVINSI] ?? null,
                    $data[UserColumns::KABUPATEN] ?? null,
                    $data[UserColumns::KECAMATAN] ?? null,
                    $data[UserColumns::JALAN] ?? null,
                    $data[UserColumns::KODE_POS] ?? null,
                    $data[UserColumns::TANGGAL_LAHIR] ?? null,
                    $data[UserColumns::BULAN_LAHIR] ?? null,
                    $data[UserColumns::TAHUN_LAHIR] ?? null,
                    $usia ?? null,
                ]
            );

            $userId = (int) DB::getPdo()->lastInsertId();

            // INSERT telepon 
            if (!empty($data['telephones'])) {
                $telephones = is_array($data['telephones']) ? $data['telephones'] : [$data['telephones']];

                foreach ($telephones as $telephone) {
                    $trimmed = trim((string) $telephone);
                    if ($trimmed !== '') {
                        DB::insert(
                            "INSERT INTO user_telephones (user_id, number, created_at, updated_at) VALUES (?, ?, ?, ?)",
                            [$userId, $trimmed, $nowString, $nowString]
                        );
                    }
                }
            }

            DB::commit();
            return $userId; // Mengembalikan ID user yang berhasil dibuat

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (str_contains($e->getMessage(), 'Duplicate entry') || 
                str_contains($e->getMessage(), '1062') ||
                str_contains($e->getMessage(), 'unique')) {
                return 'Email sudah digunakan.';
            }
            
            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }
    
    public function accounts() {
        return $this->hasMany(\App\Models\UserAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
    
    public function financialAccounts()
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts')
                    ->withPivot(['initial_balance', 'balance', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get all users using raw SQL query
     */
    public static function getAllUsersRaw()
    {
        $query = "
            SELECT
                id,
                CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name,
                email,
                nomor_telepon,
                role,
                is_active,
                created_at
            FROM users
            ORDER BY first_name ASC
        ";

        return DB::select($query);
    }
}

<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;
use App\Models\UserFinancialAccount;
use App\Models\FinancialAccount;
use App\Models\Transaction;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    

    /**
     * Insert user 
     */
    public static function createUserRaw(array $data)
    {
        if (empty($data['email'])) {
            return 'Email harus diisi.';
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
     * Setiap user memiliki satu atau beberapa akun keuangan (UserFinancialAccount)
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }

    public static function getUserAccounts($userId)
    {

        $query = "SELECT 
                    ua.id as user_account_id,
                    ua.id_user,
                    ua.username,
                    ua.email,
                    ua.verified_at,
                    ua.is_active
                  FROM user_accounts ua
                  WHERE ua.id_user = ?
                  ORDER BY ua.id";
        
        $results = DB::select($query, [$userId]);
        
      
        return array_map(function($row) {
            return [
                'user_account_id' => $row->user_account_id,
                'id_user' => $row->id_user,
                'username' => $row->username,
                'email' => $row->email,
                'verified_at' => $row->verified_at,
                'is_active' => (bool) $row->is_active
            ];
        }, $results);
    }

    /**
     * Ambil data user yang login berdasarkan user_account_id
     * Digunakan untuk endpoint /api/userlog
     */
    public static function AmbilDataUserYangLogin($userAccountId)
    {
        $query = "SELECT 
                    u.id as user_id,
                    u.name,
                    u.first_name,
                    u.middle_name,
                    u.last_name,
                    u.email as user_email,
                    u.provinsi,
                    u.kabupaten,
                    u.kecamatan,
                    u.jalan,
                    u.kode_pos,
                    u.tanggal_lahir,
                    u.bulan_lahir,
                    u.tahun_lahir,
                    u.usia,
                    ua.id as user_account_id,
                    ua.username,
                    ua.email as account_email,
                    ua.is_active,
                    ua.verified_at,
                    GROUP_CONCAT(ut.number SEPARATOR ',') as telephones
                  FROM users u
                  INNER JOIN user_accounts ua ON u.id = ua.id_user
                  LEFT JOIN user_telephones ut ON u.id = ut.user_id
                  WHERE ua.id = ?
                  AND ua.is_active = 1
                  GROUP BY u.id, u.email, u.name, u.first_name, u.middle_name, u.last_name,
                           u.provinsi, u.kabupaten, u.kecamatan, u.jalan, u.kode_pos,
                           u.tanggal_lahir, u.bulan_lahir, u.tahun_lahir, u.usia,
                           ua.id, ua.username, ua.email, ua.is_active, ua.verified_at
                  LIMIT 1";

        $userData = DB::selectOne($query, [$userAccountId]);

        if (!$userData) {
            return null;
        }

        $telephonesArray = $userData->telephones 
            ? explode(',', $userData->telephones) 
            : [];

        return [
            'user_id' => $userData->user_id,
            'user_account_id' => $userData->user_account_id,
            'username' => $userData->username,
            'email' => $userData->account_email,
            'profile' => [
                'name' => $userData->name,
                'first_name' => $userData->first_name,
                'middle_name' => $userData->middle_name,
                'last_name' => $userData->last_name,
                'tanggal_lahir' => $userData->tanggal_lahir,
                'bulan_lahir' => $userData->bulan_lahir,
                'tahun_lahir' => $userData->tahun_lahir,
                'usia' => $userData->usia,
            ],
            'address' => [
                'provinsi' => $userData->provinsi,
                'kabupaten' => $userData->kabupaten,
                'kecamatan' => $userData->kecamatan,
                'jalan' => $userData->jalan,
                'kode_pos' => $userData->kode_pos,
            ],
            'telephones' => $telephonesArray,
            'account_status' => [
                'is_active' => (bool) $userData->is_active,
                'verified_at' => $userData->verified_at,
            ]
        ];
    }

    /**
     * Ambil semua user dengan relasi telephones dan accounts
     */
    public static function GetUser()
    {
        $query = "SELECT 
                    u.id,
                    u.name,
                    u.first_name,
                    u.middle_name,
                    u.last_name,
                    u.email,
                    u.provinsi,
                    u.kabupaten,
                    u.kecamatan,
                    u.jalan,
                    u.kode_pos,
                    u.tanggal_lahir,
                    u.bulan_lahir,
                    u.tahun_lahir,
                    u.usia,
                    ut.id as telephone_id,
                    ut.number as telephone_number,
                    ua.id as user_account_id,
                    ua.username,
                    ua.email as account_email,
                    ua.verified_at,
                    ua.is_active
                  FROM users u
                  LEFT JOIN user_telephones ut ON u.id = ut.user_id
                  LEFT JOIN user_accounts ua ON u.id = ua.id_user
                  ORDER BY u.id, ut.id, ua.id";
        
        $results = DB::select($query);

        $users = [];
        foreach ($results as $row) {
            $userId = $row->id;
            
            if (!isset($users[$userId])) {
                $users[$userId] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'first_name' => $row->first_name,
                    'middle_name' => $row->middle_name,
                    'last_name' => $row->last_name,
                    'email' => $row->email,
                    'provinsi' => $row->provinsi,
                    'kabupaten' => $row->kabupaten,
                    'kecamatan' => $row->kecamatan,
                    'jalan' => $row->jalan,
                    'kode_pos' => $row->kode_pos,
                    'tanggal_lahir' => $row->tanggal_lahir,
                    'bulan_lahir' => $row->bulan_lahir,
                    'tahun_lahir' => $row->tahun_lahir,
                    'usia' => $row->usia,
                    'telephones' => [],
                    'accounts' => []
                ];
            }
            
            if ($row->telephone_id !== null) {
                $telephoneExists = false;
                foreach ($users[$userId]['telephones'] as $tel) {
                    if ($tel['number'] === $row->telephone_number) {
                        $telephoneExists = true;
                        break;
                    }
                }
                if (!$telephoneExists) {
                    $users[$userId]['telephones'][] = [
                        'number' => $row->telephone_number
                    ];
                }
            }

            if ($row->user_account_id !== null) {
                $users[$userId]['accounts'][] = [
                    'user_account_id' => $row->user_account_id,
                    'username' => $row->username,
                    'email' => $row->account_email,
                    'verified_at' => $row->verified_at,
                    'is_active' => (bool) $row->is_active
                ];
            }
        }

        return array_values($users);
    }

    public static function countUserpertanggaldanbulan(?string $startDate = null, ?string $endDate = null): array
    {
        if (!$startDate || !$endDate) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'jumlah_user' => 0,
            ];
        }

        $query = "SELECT COUNT(*) AS jumlah_user FROM users WHERE created_at >= ? AND created_at < DATE_ADD(?, INTERVAL 1 DAY)";
        $result = DB::selectOne($query, [$startDate, $endDate]);

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'jumlah_user' => isset($result->jumlah_user) ? (int) $result->jumlah_user : 0,
        ];
    }

    public static function SearchUsersbyEmailandNameandid($searchTerm)
    { 
        if (empty($searchTerm)) {
            return [];
        }

        $params = [];
        $likeTerm = '%' . $searchTerm . '%';

        $query = "SELECT 
                    u.id,
                    u.name,
                    u.first_name,
                    u.middle_name,
                    u.last_name,
                    u.email,
                    u.provinsi,
                    u.kabupaten,
                    u.kecamatan,
                    u.jalan,
                    u.kode_pos,
                    u.tanggal_lahir,
                    u.bulan_lahir,
                    u.tahun_lahir,
                    u.usia,
                    u.created_at,
                    u.updated_at
                    FROM users u
                    WHERE ";

        if (strpos($searchTerm, '@') !== false) {
            $query .= "u.email LIKE ?";
            $params[] = $likeTerm;
        } elseif (is_numeric($searchTerm)) {
            $query .= "u.id = ?";
            $params[] = $searchTerm;
        } else {
            $query .= "(u.name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $params = [$likeTerm, $likeTerm, $likeTerm];
        }

        try {
            return DB::select($query, $params);
        } catch (\Exception $e) {
            Log::error('Search Users Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update user: name, email, password, photo, preference
     */
    public static function updateUserRaw(int $userId, array $data)
    {
        try {
            DB::beginTransaction();

            $fields = [];
            $values = [];

            if (isset($data['name'])) {
                $fields[] = "name = ?";
                $values[] = $data['name'];
            }

            if (isset($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    return 'Format email tidak valid.';
                }

                $fields[] = "email = ?";
                $values[] = $data['email'];
            }

            if (empty($fields)) {
                return 'Tidak ada data yang diupdate.';
            }

            $values[] = $userId;

            DB::update(
                "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
                $values
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return 'Gagal update user: ' . $e->getMessage();
        }
    }
}

    public static function updateUserRaw(int $userId, array $data)
    {
        try {
            DB::beginTransaction();

            $fields = [];
            $values = [];

            if (isset($data['name'])) {
                $fields[] = "name = ?";
                $values[] = $data['name'];
            }

            if (isset($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    return 'Format email tidak valid.';
                }

                $fields[] = "email = ?";
                $values[] = $data['email'];
            }

            if (empty($fields)) {
                return 'Tidak ada data yang diupdate.';
            }

            $values[] = $userId;

            DB::update(
                "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
                $values
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return 'Gagal update user: ' . $e->getMessage();
        }
    }
}
                "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
                $values
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return 'Gagal update user: ' . $e->getMessage();
        }
    }

}
>>>>>>> 7575c0db0db92cb0c3f9ed3942aecd66dc6fffb2

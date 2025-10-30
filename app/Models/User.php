<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = [
        'nama_awal',
        'nama_tengah', 
        'nama_akhir',
        'username',
        'email',
        'password',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos',
        'tanggal_lahir'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['nama_lengkap', 'usia'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }

    // Relasi dengan telepon
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }

    // Accessor untuk usia
    public function getUsiaAttribute()
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return \Carbon\Carbon::parse($this->tanggal_lahir)->age;
    }

    // Accessor untuk nama lengkap
    public function getNamaLengkapAttribute()
    {
        return trim($this->nama_awal . ' ' . 
            ($this->nama_tengah ? $this->nama_tengah . ' ' : '') . 
            $this->nama_akhir);
    }

    public static function createUserRaw(array $data)
    {
        $checkQuery = "SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1";
        $exists = DB::select($checkQuery, [$data['email'], $data['username']]);
        
        if (!empty($exists)) {
            return 'Email atau Username sudah digunakan.';
        }

        try {
            DB::beginTransaction();

            // Insert user (tanpa nomor_telepon)
            $insertQuery = "
                INSERT INTO users 
                    (nama_awal, nama_tengah, nama_akhir, username, email, password, 
                    provinsi, kabupaten, kecamatan, jalan, kode_pos, 
                    tanggal_lahir, created_at, updated_at) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $hashedPassword = Hash::make($data['password']);
            $now = now();

            DB::insert($insertQuery, [
                $data['nama_awal'],
                $data['nama_tengah'] ?? null,
                $data['nama_akhir'],
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['provinsi'] ?? null,
                $data['kabupaten'] ?? null,
                $data['kecamatan'] ?? null,
                $data['jalan'] ?? null,
                $data['kode_pos'] ?? null,
                $data['tanggal_lahir'] ?? null,
                $now,
                $now
            ]);

            $userId = DB::getPdo()->lastInsertId();

            // Insert nomor telepon jika ada
            if (isset($data['nomor_telepon']) && !empty($data['nomor_telepon'])) {
                $telephones = is_array($data['nomor_telepon']) ? $data['nomor_telepon'] : [$data['nomor_telepon']];
                
                foreach ($telephones as $telephone) {
                    if (!empty(trim($telephone))) {
                        DB::insert(
                            "INSERT INTO user_telephones (user_id, number, created_at, updated_at) VALUES (?, ?, ?, ?)",
                            [$userId, trim($telephone), $now, $now]
                        );
                    }
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }
}
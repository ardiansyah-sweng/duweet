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

    protected $fillable = [
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
        'nomor_telepon',
        'tanggal_lahir'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }

    // Method untuk usia
    public function usia()
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
            $insertQuery = "
                INSERT INTO users 
                    (nama_awal, nama_tengah, nama_akhir, username, email, password, 
                    provinsi, kabupaten, kecamatan, jalan, kode_pos, nomor_telepon, 
                    tanggal_lahir, created_at, updated_at) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                $data['nomor_telepon'] ?? null,
                $data['tanggal_lahir'] ?? null,
                $now,
                $now
            ]);

            return true; 

        } catch (\Exception $e) {
            return 'Gagal menyimpan user ke database: ' . $e->getMessage();
        }
    }
}
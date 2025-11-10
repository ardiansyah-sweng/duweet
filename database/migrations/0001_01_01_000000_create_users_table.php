<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'ID_User';

    protected $fillable = [
        'nama_awal',
        'nama_tengah',
        'nama_akhir',
        'email',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos',
        'nomor_telepon',
        'tanggal',
        'bulan',
        'tahun',
    ];

    protected $casts = [
        'nomor_telepon' => 'array', 
    ];

 
    public function usia()
    {

        $tanggal_lahir = Carbon::createFromDate($this->tahun, $this->bulan, $this->tanggal);
        return $tanggal_lahir->age;
    }

    public function getNamaLengkapAttribute()
    {
        return trim("{$this->nama_awal} {$this->nama_tengah} {$this->nama_akhir}");
    }
}

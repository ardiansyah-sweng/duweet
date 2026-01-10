<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashout extends Model
{
    use HasFactory;

    protected $table = 'cashouts';  // sesuaikan jika tabel Anda berbeda

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'note',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User (jika dibutuhkan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Filter berdasarkan periode
    public function scopeBetweenDates($query, $start, $end)
    {
        $start = $start . ' 00:00:00';
        $end   = $end . ' 23:59:59';
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserPerTanggalBulan extends Model
{
    /**
     * Hitung jumlah user per tanggal (berdasarkan created_at)
     */
    public static function countPerTanggal()
    {
        return DB::table('users')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();
    }

    /**
     * Hitung jumlah user per bulan dan tahun
     */
    public static function countPerBulan()
    {
        return DB::table('users')
            ->select(
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Count User Per Tanggal (SQLite compatible)
    public function countUserPerTanggal()
    {
        $usersPerTanggal = User::select(
                DB::raw("DATE(created_at) as tanggal"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('tanggal', 'ASC')
            ->get();

        return view('userstats.per_tanggal', compact('usersPerTanggal'));
    }

    // Count User Per Bulan (SQLite compatible)
    public function countUserPerBulan()
    {
        $usersPerBulan = User::select(
                DB::raw("strftime('%m', created_at) as bulan"),
                DB::raw("strftime('%Y', created_at) as tahun"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy(DB::raw("strftime('%Y', created_at)"), DB::raw("strftime('%m', created_at)"))
            ->orderBy('tahun', 'ASC')
            ->orderBy('bulan', 'ASC')
            ->get();

        return view('userstats.per_bulan', compact('usersPerBulan'));
    }
}

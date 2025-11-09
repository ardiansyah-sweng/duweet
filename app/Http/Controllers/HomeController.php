<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home'); // nanti kita buat file home.blade.php di folder resources/views
    }
}

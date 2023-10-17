<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\FlareClient\View;

class AdministrasiController extends Controller
{
    public function index()
    {
        return view('staff.analisa.administrasi');
    }
}

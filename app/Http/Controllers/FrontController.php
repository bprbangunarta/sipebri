<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        return view('front-end.index');
    }

    public function pengajuan()
    {
        return view('front-end.pengajuan');
    }

    public function tracking()
    {
        return view('front-end.tracking');
    }

    public function verifikasi()
    {
        return view('front-end.qr-code');
    }
}
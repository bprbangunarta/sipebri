<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        return view('pendaftaran.index');
    }

    public function edit(Request $request)
    {
        return view('pendaftaran.edit');
    }
}

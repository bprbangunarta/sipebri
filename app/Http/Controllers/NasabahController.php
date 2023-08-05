<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NasabahController extends Controller
{
    public function edit(Request $request)
    {
        return view('nasabah.edit');
    }

    public function validasi(Request $request)
    {
        return view('nasabah.validasi');
    }
}

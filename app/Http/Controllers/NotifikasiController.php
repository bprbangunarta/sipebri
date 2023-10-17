<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function data_penolakan()
    {
        return view('notifikasi.penolakan.index');
    }

    public function tambah_penolakan()
    {
        return view('notifikasi.penolakan.tambah');
    }
}

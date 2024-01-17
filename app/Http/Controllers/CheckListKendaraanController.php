<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CheckListKendaraanController extends Controller
{
    public function index()
    {
        return view('analisa.check-list-kendaraan.index');
    }

    public function report_kih()
    {
        return view('cetak-berkas.cek-kendaraan.report_kih');
    }

    public function report_kko()
    {
        return view('cetak-berkas.cek-kendaraan.report_kko');
    }

    public function report_kpj()
    {
        return view('cetak-berkas.cek-kendaraan.report_kpj');
    }
}

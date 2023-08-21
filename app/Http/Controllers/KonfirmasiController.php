<?php

namespace App\Http\Controllers;

use App\Models\Agunan;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfirmasiController extends Controller
{
    public function index(Request $request){
        $nasabah = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $nasabah)->get();
        $konfirmasi = DB::table('v_validasi_pengajuan')
                        ->where('kode_nasabah', $nasabah)->get();               
        return view('pengajuan.konfirmasi', [
            'data' => $cek[0],
            'konfirmasi' => $konfirmasi[0],
        ]);
    }

    public function konfirmasi(Request $request){
        dd($request);
    }
}

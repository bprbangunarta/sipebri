<?php

namespace App\Http\Controllers;

use App\Models\Agunan;
use App\Models\Nasabah;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{

    public function index(Request $request)
    {
       $query = Pengajuan::join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_pengajuan.kode_pengajuan as kode','data_pengajuan.nasabah_kode as kd_nasabah', 'data_pengajuan.plafon as plafon', 'data_pengajuan.jangka_waktu as jk', 'data_nasabah.nama_nasabah as nama', 'data_nasabah.alamat_ktp as alamat')->get();
    
        return view('pengajuan.index',[
            'data' => $query,
        ]);
    }

    public function edit(Request $request)
    {
        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();
        // dd($cek);
        return view('pengajuan.edit',[
            'data' =>$cek
        ]);
    }

    public function agunan(Request $request)
    {
        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();

        $agunan = Agunan::all();
        $dok = DB::table('data_jenis_dokumen')->get();
        return view('pengajuan.agunan', [
            'agunan' => $agunan,
            'dok' => $dok,
            'data' => $cek
        ]);
    }
}

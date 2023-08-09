<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Pendamping;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class PendampingController extends Controller
{
    public function edit(Request $request)
    {
        $nasabah = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $nasabah)->first();
        
        //Ambil kode pengajuan
        $pengajuan = Pengajuan::where('nasabah_kode', $cek->kode_nasabah)->get();

        //Ambil kode pendamping
        $pendamping = Pendamping::where('pengajuan_kode',$pengajuan[0]->kode_pengajuan)->get();
        // dd($pendamping);

        return view('pendamping.edit', [
            'nasabah' => $cek
        ]);
    }
}

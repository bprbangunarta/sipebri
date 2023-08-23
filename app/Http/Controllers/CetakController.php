<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakController extends Controller
{
    public function pengajuan(Request $request){
        $kode = $request->query('pengajuan');
        
        return view('cetak.pengajuan',[
            'data' => $kode
        ]);
    }

    public function nik(Request $request){
        $kode = $request->query('cetak');
        $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $kode)->get();

        //Hari ini
        $hari = Carbon::today();
        $data[0]->hari = $hari->isoformat('D MMMM Y');
        
        return view('cetak.layouts.nik',[
            'data' => $data[0]
        ]);
    }
}

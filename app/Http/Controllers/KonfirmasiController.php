<?php

namespace App\Http\Controllers;

use App\Models\Agunan;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class KonfirmasiController extends Controller
{
    public function index(Request $request){
        $nasabah = $request->query('nasabah');
        $enc = Crypt::decrypt($nasabah);
        
        $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
        
        $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->get();
        $konfirmasi = DB::table('v_validasi_pengajuan')
                        ->where('kode_pengajuan', $enc)->get();   
        $cek[0]->kd_pengajuan = $nasabah;  
        
        return view('pengajuan.konfirmasi', [
            'data' => $cek[0],
            'konfirmasi' => $konfirmasi[0],
        ]);
    }

    public function konfirmasi(Request $request){
        $nasabah = $request->query('konfirmasi');
        $enc = Crypt::decrypt($nasabah);
        $cek = [
            'nasabah' => $request->nasabah,
            'pendamping' => $request->pendamping,
            'pengajuan' => $request->pengajuan,
            'survei' => $request->survei,
        ];

        //Cek data apakah sudah ceklis semua apa belum
        foreach ($cek as $key => $value) {
            if ($value == "0") {
                return redirect()->back()->with('error', 'Data harus diisi sesuai dengan ketentuan');
            }
        }

        //Data auth
        $data = [
            'auth_user' => Auth::user()->code_user,
            'status' => 'Minta Otorisasi',
        ];
        
        try {
            $nas = Pengajuan::where('kode_pengajuan', $enc)->get();
            Pengajuan::where('id', $nas[0]->id)->update($data);
            return redirect()->back()->with('success', 'Status telah diperbaharui');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ada data yang tidak terisi');
        }
    }

    public function otorisasi(Request $request){
        $nasabah = $request->query('nasabah');
        $enc = Crypt::decrypt($nasabah);
        
        $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
        
        $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->get();
        $otorisasi = DB::table('v_validasi_pengajuan')
                        ->where('kode_pengajuan', $enc)->get();
        
        $cek[0]->kd_pengajuan = $nasabah;
        
        return view('pengajuan.otorisasi', [
            'data' => $cek[0],
            'otorisasi' => $otorisasi[0],
        ]);
    }

    public function validasiotor(Request $request){
        $nasabah = $request->query('validasiotor');
        $enc = Crypt::decrypt($nasabah);
        $data = Pengajuan::where('kode_pengajuan', $enc)->get();
        
        //Cek Status hanya Minta Otorisasi
        if ($data[0]->status != "Minta Otorisasi") {
           return redirect()->back()->with('error', 'Anda tidak diizinkan melakukan Otorisasi');
        }

        $cek = [
            'nasabah' => $request->nasabah,
            'pendamping' => $request->pendamping,
            'pengajuan' => $request->pengajuan,
            'survei' => $request->survei,
        ];
       
        $data = [
            'auth_user' => Auth::user()->code_user,
            'status' => 'Sudah Otorisasi',
        ];
        
        //Cek data apakah sudah ceklis semua apa belum
        foreach ($cek as $value) {
            if ($value == "0") {
                return redirect()->back()->with('error', 'Data harus diisi sesuai dengan ketentuan');
            }
        }

        try {
            $nas = Pengajuan::where('kode_pengajuan', $nasabah)->get();
            Pengajuan::where('id', $nas[0]->id)->update($data);
            return redirect()->back()->with('success', 'Status telah diperbaharui');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ada data yang tidak terisi');
        }
    }
}

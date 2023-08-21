<?php

namespace App\Http\Controllers;

use App\Models\Agunan;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $nasabah = $request->query('konfirmasi');
        
        $cek = [
            'nasabah' => $request->nasabah,
            'pendamping' => $request->pendamping,
            'pengajuan' => $request->pengajuan,
            'survei' => $request->survei,
        ];

        //Cek data apakah sudah ceklis sm=emua apa belum
        foreach ($cek as $key => $value) {
            if ($value == "0") {
                return redirect()->back()->with('error', 'Data harus diisi sesuai dengan ketentuan');
            }
        }

        //Data auth
        $us = Auth::user()->id;
        $user = DB::table('v_users')
                    ->select('code_user')
                    ->where('user_id', $us)->get();

        $data = [
            'auth_user' => $user[0]->code_user,
            'status' => 'Minta Otorisasi',
        ];

        try {
            $nas = Nasabah::where('kode_nasabah', $nasabah)->get();
            Nasabah::where('id', $nas[0]->id)->update($data);
            return redirect()->back()->with('success', 'Status telah diperbaharui');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ada data yang tidak terisi');
        }
    }

    public function otorisasi(Request $request){
        $nasabah = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $nasabah)->get();
        $otorisasi = DB::table('v_validasi_pengajuan')
                        ->where('kode_nasabah', $nasabah)->get();
        // dd($otorisasi);
        return view('pengajuan.otorisasi', [
            'data' => $cek[0],
            'otorisasi' => $otorisasi[0],
        ]);
    }

    public function validasiotor(Request $request){
        $nasabah = $request->query('validasiotor');
        $data = Nasabah::where('kode_nasabah', $nasabah)->get();

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
            'status' => 'Sudah Otorisasi',
        ];
        
        //Cek data apakah sudah ceklis sm=emua apa belum
        foreach ($cek as $value) {
            if ($value == "0") {
                return redirect()->back()->with('error', 'Data harus diisi sesuai dengan ketentuan');
            }
        }

        try {
            $nas = Nasabah::where('kode_nasabah', $nasabah)->get();
            Nasabah::where('id', $nas[0]->id)->update($data);
            return redirect()->back()->with('success', 'Status telah diperbaharui');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ada data yang tidak terisi');
        }
    }
}

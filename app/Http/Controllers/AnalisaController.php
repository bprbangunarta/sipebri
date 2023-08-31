<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaController extends Controller
{
    public function index()
    {
        $usr = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
                ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
                ->where('data_survei.surveyor_kode', '=', $usr)
                ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2','users.name')->get();
        
        //Enkripsi kode pengajuan
        $cek[0]->kd_pengajuan = Crypt::encrypt($cek[0]->kode_pengajuan);
        
        return view('analisa.proses', [
            'data' => $cek
        ]);
    }

    public function analisa_usaha_perdagangan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            
            
            return view('analisa.usaha.perdagangan', [
                'data' => $cek[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }

    public function analisa_usaha_pertanian(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $a = $request->query('pengajuan');
            
            return view('analisa.usaha.pertanian', [
                'data' => $cek[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }
     
    public function analisa_usaha_jasa(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            
            return view('analisa.usaha.jasa', [
                'data' => $cek[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }

    
    public function analisa_usaha_lainnya(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            
            return view('analisa.usaha.lainnya', [
                'data' => $cek[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }
    
    public function analisa_keuangan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            
            return view('analisa.keuangan', [
                'data' => $cek[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }

    public function analisa_usaha_perdagangan_detail(Request $request)
    {   
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            return view('analisa.usaha.perdagangan-detail', [
                'data' => $cek[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }
}

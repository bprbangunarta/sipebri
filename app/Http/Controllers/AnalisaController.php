<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use App\Models\Lain;
use App\Models\Midle;
use App\Models\Pengajuan;
use App\Models\Pertanian;
use App\Models\Perdagangan;
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
                ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2','users.name');
        
        //Enkripsi kode pengajuan
        
        $data = $cek->paginate(10);
        if ($data->isNotEmpty()) {
            $data[0]->kd_pengajuan = Crypt::encrypt($data[0]->kode_pengajuan);
        }   
       
        return view('analisa.proses', [
            'data' => $data
        ]);
    }

    public function analisa_usaha_perdagangan(Request $request)
    {
    
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Perdagangan::au_perdagangan($enc);
            
            foreach($au as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
                     
            return view('analisa.usaha.perdagangan', [
                'data' => $cek[0],
                'perdagangan' => $au
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
            $au = Pertanian::au_pertanian($enc); 
            
            foreach($au as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
            
            return view('analisa.usaha.pertanian', [
                'data' => $cek[0],
                'pertanian' => $au
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
            $au = Jasa::au_jasa($enc); 
            
            foreach($au as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
            
            return view('analisa.usaha.jasa', [
                'data' => $cek[0],
                'jasa' => $au
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
            $au = Lain::au_lain($enc);

            foreach($au as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
            
            return view('analisa.usaha.lainnya', [
                'data' => $cek[0],
                'lain' => $au,
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
            
            $kemampuan = Midle::kemampuan_keuangan($enc);

            //buat array
            $data = [
              'perdagangan' => $kemampuan->perdagangan,  
              'pertanian' => $kemampuan->pertanian,  
              'jasa' => $kemampuan->jasa, 
              'lainnya' => $kemampuan->lainnya,
            ];
            $filter = array_filter($data, function ($value) {
                return $value !== null;
            });
            //Hasil penjumlahan analisa usaha
            $total = array_sum($filter);
            $kemampuan->total = $total;
           
            return view('analisa.keuangan', [
                'data' => $cek[0],
                'kemampuan' => $kemampuan
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }

    //=====Function detail analisa=====//
    public function analisa_usaha_perdagangan_detail(Request $request)
    {   
        
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));
            
            
            $cek = Midle::analisa_usaha($encpengajuan);
            $cek[0]->kd_nasabah = $request->query('usaha');

            //Data perdagangan
            $perdagangan = Midle::perdagangan_detail($request->query('usaha'));
            
            //Jika data kosong maka ke view baru
            if (count($perdagangan[1]) == 0) {
                return view('analisa.usaha.perdagangan-detail-kosong', [
                'data' => $cek[0],
            ]);
            } 
            
            return view('analisa.usaha.perdagangan-detail', [
                'data' => $cek[0],
                'datausaha' => $perdagangan[0],
                'perdagangan' => $perdagangan[1],
            ]);
            
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
       
    }

    public function analisa_usaha_pertanian_detail(Request $request)
    {   
        
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            
            return view('analisa.usaha.pertanian-detail', [
                'data' => $cek[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
       
    }

    public function analisa_usaha_jasa_detail(Request $request)
    {   
        
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $usaha = Crypt::decrypt($request->query('usaha'));
            $cek = Midle::analisa_usaha($enc);

            $jasa = Midle::jasa_detail($usaha);
            $jasa[0]->kd_usaha = Crypt::encrypt($jasa[0]->kode_usaha);
            
            return view('analisa.usaha.jasa-detail', [
                'data' => $cek[0],
                'jasa' => $jasa[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
       
    }

    public function analisa_usaha_lainnya_detail(Request $request)
    {   
        
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            
            return view('analisa.usaha.lainnya-detail', [
                'data' => $cek[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
       
    }
}

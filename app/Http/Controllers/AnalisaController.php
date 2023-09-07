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
}

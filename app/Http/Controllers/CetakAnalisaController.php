<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CetakAnalisaController extends Controller
{
    public function analisa5c(Request $request)
    {
        $kode = $request->query('pengajuan');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                    ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                    ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_nasabah.nama_nasabah', 'data_pengajuan.produk_kode', 'data_pengajuan.metode_rps', 'data_pengajuan.jangka_bunga', 'data_pengajuan.jangka_waktu')
                    ->where('data_pengajuan.kode_pengajuan', '=', $enc)->first();
            
            $data->kd_pengajuan = $kode;
            
            //Hari
            $hari = Carbon::today();
            $data->hari = $hari->isoformat('D MMMM Y');

            //Format Angka
            $format_angka = "Rp. " . number_format($data->plafon, 0, ',', '.');
            $data->rp_plafon = $format_angka;
            
            return view('cetak.analisa.index',[
                'data' => $data
            ]);

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function usaha_jasa(Request $request)
    {
        $kode = $request->query('pengajuan');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            // dd($kode);
            $data = DB::table('data_pengajuan')
                    ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                    ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah')
                    ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');
            
            return view('cetak.analisa.usaha_jasa',[
                'data' => $data[0]
            ]);

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function kemampuan_keuangan(Request $request)
    {
        $kode = $request->query('cetak');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            
            return view('cetak.analisa.kemampuan_keuangan');

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetakanalisa5c(Request $request)
    {
        $kode = $request->query('cetak');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            
            return view('cetak.analisa.analisa5c');

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function crr(Request $request)
    {
        $kode = $request->query('cetak');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            
            return view('cetak.analisa.credit_risk_rating');

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

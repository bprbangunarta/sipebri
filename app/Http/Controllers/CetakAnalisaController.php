<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Carbon\Carbon;
use App\Models\Jasa;
use App\Models\Midle;
use App\Models\Keuangan;
use App\Models\Kepemilikan;
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
            
            $data = Jasa::cetak_jasa($enc);

            //Hari ini
            $hari = Carbon::today();
            $data[0][0]->hari = $hari->isoformat('D MMMM Y');
            
            return view('cetak.analisa.usaha_jasa',[
                'data' => $data[0][0],
                'jasa' => $data[1],
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
            $data = Keuangan::cetak_keuangan($enc);
            $biaya = Keuangan::data_keuangan($enc);
            $usaha = Midle::kemampuan_keuangan($enc);
            $kepemilikan = Kepemilikan::where('pengajuan_kode', $enc)->first();
            $taksasi = Midle::taksasi($enc);
            
            $filter = array_filter($usaha, function ($value) {
                return $value !== null ? $value : 0;
            });

            //jumlah hasil pengeluaran kemampuan keuangan
            $tbiaya = [];
            for ($i=0; $i < count($biaya); $i++) { 
                $tbiaya [] += $biaya[$i]->nominal;
            }
            $totalbiaya = array_sum($tbiaya);
            
            //Hasil penjumlahan analisa usaha
            $total = array_sum($filter);
            $usaha['total'] = $total;
            $kemampuanperbulan = $usaha['total'] - $totalbiaya;

            return view('cetak.analisa.kemampuan_keuangan',[
                'data' => $data[0],
                'usaha' => $usaha,
                'biaya' => $biaya,
                'totalbiaya' => $totalbiaya,
                'kemampuanperbulan' => $kemampuanperbulan,
                'kepemilikan' => $kepemilikan,
                'taksasi' => $taksasi,
            ]);

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

    public function tambahan(Request $request)
    {
        $kode = $request->query('cetak');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            
            return view('cetak.analisa.tambahan');

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_memorandum(Request $request)
    {
        $kode = $request->query('cetak');
        
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            
            return view('cetak.analisa.memorandum_usulan');

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

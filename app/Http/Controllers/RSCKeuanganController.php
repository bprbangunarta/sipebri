<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCKeuanganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $kemampuan = $this->kemampuan_keuangan($enc_rsc);
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_telp',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                    'data_spk.no_spk',
                    'data_spk.created_at',
                    'data_spk.updated_at',
                )
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->get();
            //
            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            $filter = array_filter($kemampuan, function ($value) {
                return $value !== null;
            });
            $total = array_sum($filter);

            $rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $enc)->where('kode_rsc', $enc_rsc)->first();

            dd($data, $rsc);
            return view('rsc.keuangan.index', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }



    private function kemampuan_keuangan($data)
    {

        $perdagangan = DB::table('rsc_au_perdagangan')->where('kode_rsc', $data)->get();
        $jasa = DB::table('rsc_au_jasa')->where('kode_rsc', $data)->get();
        $pertanian = DB::table('rsc_au_pertanian')->where('kode_rsc', $data)->get();
        $lain = DB::table('rsc_au_lain')->where('kode_rsc', $data)->get();

        $tani = [];
        for ($i = 0; $i < count($pertanian); $i++) {
            $tani[] = $pertanian[$i]->laba_perbulan ?? 0;
        }
        //Hasil penjumlahan analisa usaha pertanian
        $totalpertanian = array_sum($tani);

        $dagang = [];
        for ($j = 0; $j < count($perdagangan); $j++) {
            $dagang[] = $perdagangan[$j]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha perdagangan
        $totalperdagangan = array_sum($dagang);

        $js = [];
        for ($k = 0; $k < count($jasa); $k++) {
            $js[] = $jasa[$k]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha jasa
        $totaljasa = array_sum($js);

        $la = [];
        for ($l = 0; $l < count($lain); $l++) {
            $la[] = $lain[$l]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha jasa
        $totallain = array_sum($la);

        $hasil = [
            'perdagangan' => $totalperdagangan,
            'jasa' => $totaljasa,
            'pertanian' => $totalpertanian,
            'lain' => $totallain,
        ];

        return $hasil;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
            $kemampuan['total'] = $total;

            $analisa_keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();
            // dd($analisa_keuangan);
            if (is_null($analisa_keuangan)) {
                $keuangan = $this->data_static();
            } else {
                $keuangan = $analisa_keuangan;
            }

            return view('rsc.keuangan.index', [
                'data' => $data[0],
                'kemampuan' => $kemampuan,
                'keuangan' => $keuangan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_keuangan(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->query('rsc'));
            $kode = $this->kodeacak('AUK', 6);
            $data_keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $rsc)->first();
            $data = [
                'kode_rsc' => $rsc,
                'kode_keuangan' => $kode,
                'konsumsi' => (int)str_replace(["Rp", " ", "."], "", $request->biaya1),
                'kesehatan' => (int)str_replace(["Rp", " ", "."], "", $request->biaya2),
                'pendidikan' => (int)str_replace(["Rp", " ", "."], "", $request->biaya3),
                'gatel' => (int)str_replace(["Rp", " ", "."], "", $request->biaya4),
                'jajan_anak' => (int)str_replace(["Rp", " ", "."], "", $request->biaya5),
                'sumbangan' => (int)str_replace(["Rp", " ", "."], "", $request->biaya6),
                'roko' => (int)str_replace(["Rp", " ", "."], "", $request->biaya7),
                'kewajiban1' => $request->data1,
                'kewajiban2' => $request->data2,
                'kewajiban3' => $request->data3,
                'nominal_kewajiban1' => (int)str_replace(["Rp", " ", "."], "", $request->kewajiban1),
                'nominal_kewajiban2' => (int)str_replace(["Rp", " ", "."], "", $request->kewajiban2),
                'nominal_kewajiban3' => (int)str_replace(["Rp", " ", "."], "", $request->kewajiban3),
                'p_usaha' => (int)str_replace(["Rp", " ", "."], "", $request->p_usaha),
                'b_rumah_tangga' => (int)str_replace(["Rp", " ", "."], "", $request->b_rumah_tangga),
                'b_kewajiban_lainnya' => (int)str_replace(["Rp", " ", "."], "", $request->b_kewajiban_lainya),
                'keuangan_perbulan' => (int)str_replace(["Rp", " ", "."], "", $request->keuangan_perbulan),
                'input_user' => Auth::user()->code_user,
            ];

            if (is_null($data_keuangan)) {
                $data['created_at'] = now();
                $insert = DB::table('rsc_analisa_keuangan')->insert($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Data keuangan berhasil ditambahkan.');
                } else {
                    return redirect()->back()->with('error', 'Data keuangan gagal ditambahkan.');
                }
            } else {
                $data['updated_at'] = now();
                $update = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $rsc)->update($data);
                if ($update) {
                    return redirect()->back()->with('success', 'Data keuangan berhasil diubah.');
                } else {
                    return redirect()->back()->with('error', 'Data keuangan gagal diubah.');
                }
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }




    private function data_static()
    {
        $data = (object) [
            'konsumsi' => 0,
            'kesehatan' => 0,
            'pendidikan' => 0,
            'gatel' => 0,
            'jajan_anak' => 0,
            'sumbangan' => 0,
            'roko' => 0,
            'pajak' => 0,
            'kewajiban1' => null,
            'kewajiban2' => null,
            'kewajiban3' => null,
            'nominal_kewajiban1' => 0,
            'nominal_kewajiban2' => 0,
            'nominal_kewajiban3' => 0,
            'b_rumah_tangga' => 0,
            'b_kewajiban_lainnya' => 0,
            'keuangan_perbulan' => 0,
        ];

        return $data;
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

    private function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('rsc_au_lain')->where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }
        return null;
    }
}

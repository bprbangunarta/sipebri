<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Spatie\FlareClient\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AdministrasiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $apht_fiducia = Midle::perhitungan_apht_fiducia($cek[0]->kode_pengajuan);

            //cek data ada atau tidak
            $administrasi = DB::table('a_administrasi')->where('pengajuan_kode', $enc)->first();

            //Biaya Provisi
            $provisi = ($cek[0]->plafon * $cek[0]->b_provisi) / 100;
            $cek[0]->provisi = (int)$provisi;

            if (is_null($apht_fiducia)) {
                $adm = ($cek[0]->plafon * $cek[0]->b_admin) / 100;
                $cek[0]->administrasi = (int)$adm;
                $cek[0]->apht = 0;
                $cek[0]->fiducia = 0;
            } else {
                $cek[0]->administrasi = $apht_fiducia->adm;
                $cek[0]->apht = $apht_fiducia->apht;
                $cek[0]->fiducia = $apht_fiducia->fiducia;
            }

            if (is_null($administrasi)) {
                return view('staff.analisa.administrasi', [
                    'data' => $cek[0],
                ]);
            }

            $administrasi = DB::table('a_administrasi')
                ->leftJoin('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'a_administrasi.pengajuan_kode')
                ->select('a_administrasi.*', 'a_memorandum.by_fiducia as fiducia')
                ->where('a_administrasi.pengajuan_kode', $enc)->first();

            //Perubahan Realtime Nominal administrasi
            if ($cek[0]->administrasi != $administrasi->administrasi) {
                $adm_real = ['administrasi' => $cek[0]->administrasi];
                DB::table('a_administrasi')->where('pengajuan_kode', $enc)->update($adm_real);
            }

            //Perubahan Realtime Nominal Provisi
            if ($cek[0]->provisi != $administrasi->provisi) {
                $provisi_real = ['provisi' => $cek[0]->provisi];
                DB::table('a_administrasi')->where('pengajuan_kode', $enc)->update($provisi_real);
            }

            //Perubahan Realtime APHT
            if ($cek[0]->apht != $administrasi->proses_apht) {
                $apht_real = ['proses_apht' => $cek[0]->apht];
                DB::table('a_administrasi')->where('pengajuan_kode', $enc)->update($apht_real);
            }

            //Perubahan Realtime FIDUCIA
            // if ($cek[0]->fiducia != $administrasi->proses_apht) {
            //     $apht_real = ['proses_apht' => $cek[0]->apht];
            //     DB::table('a_administrasi')->where('pengajuan_kode', $enc)->update($apht_real);
            // }

            $administrasi = DB::table('a_administrasi')
                ->leftJoin('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'a_administrasi.pengajuan_kode')
                ->select('a_administrasi.*', 'a_memorandum.by_fiducia as fiducia')
                ->where('a_administrasi.pengajuan_kode', $enc)->first();
            //
            // dd($cek[0], $administrasi);
            return view('staff.analisa.administrasi-edit', [
                'data' => $cek[0],
                'adm' => $administrasi,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $name = 'ADM';
            $length = 5;
            $kode = Midle::kodeacak_adm($name, $length);
            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $enc,
                'administrasi' => (int)str_replace(["Rp", " ", "."], "", $request->administrasi),
                'provisi' =>  (int)str_replace(["Rp", " ", "."], "", $request->provisi),
                'materai' =>  (int)str_replace(["Rp", " ", "."], "", $request->materai),
                'asuransi_jiwa_menurun1' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_menurun1),
                'asuransi_jiwa_menurun2' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_menurun2),
                'asuransi_jiwa_menurun3' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_menurun3),
                'asuransi_jiwa_tetap1' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_tetap1),
                'asuransi_jiwa_tetap2' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_tetap2),
                'asuransi_jiwa' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa),
                'asuransi_kendaraan_motor' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_kendaraan_motor),
                'transaksi_kredit' =>  (int)str_replace(["Rp", " ", "."], "", $request->transaksi_kredit),
                'proses_shm' =>  (int)str_replace(["Rp", " ", "."], "", $request->proses_shm),
                'polis_materai' =>  (int)str_replace(["Rp", " ", "."], "", $request->polis_materai),
                'pajak_stnk' =>  (int)str_replace(["Rp", " ", "."], "", $request->pajak_stnk),
                'proses_apht' =>  (int)str_replace(["Rp", " ", "."], "", $request->proses_apht),
                'lainnya' =>  (int)str_replace(["Rp", " ", "."], "", $request->lainnya),
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            $data2 = [
                'by_fiducia' =>  (int)str_replace(["Rp", " ", "."], "", $request->by_fiducia),
            ];

            DB::transaction(function () use ($data, $data2, $enc) {
                DB::table('a_administrasi')->insert($data);
                DB::table('a_memorandum')->where('pengajuan_kode', $enc)->update($data2);
            });

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function update(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'administrasi' => (int)str_replace(["Rp", " ", "."], "", $request->administrasi),
                'provisi' =>  (int)str_replace(["Rp", " ", "."], "", $request->privisi),
                'materai' =>  (int)str_replace(["Rp", " ", "."], "", $request->materai),
                'asuransi_jiwa_menurun1' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_menurun1),
                'asuransi_jiwa_menurun2' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_menurun2),
                'asuransi_jiwa_menurun3' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_menurun3),
                'asuransi_jiwa_tetap1' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_tetap1),
                'asuransi_jiwa_tetap2' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa_tetap2),
                'asuransi_jiwa' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_jiwa),
                'asuransi_kendaraan_motor' =>  (int)str_replace(["Rp", " ", "."], "", $request->asuransi_kendaraan_motor),
                'transaksi_kredit' =>  (int)str_replace(["Rp", " ", "."], "", $request->transaksi_kredit),
                'proses_shm' =>  (int)str_replace(["Rp", " ", "."], "", $request->proses_shm),
                'polis_materai' =>  (int)str_replace(["Rp", " ", "."], "", $request->polis_materai),
                'pajak_stnk' =>  (int)str_replace(["Rp", " ", "."], "", $request->pajak_stnk),
                'proses_apht' =>  (int)str_replace(["Rp", " ", "."], "", $request->proses_apht),
                'lainnya' =>  (int)str_replace(["Rp", " ", "."], "", $request->lainnya),
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            $data2 = [
                'by_fiducia' =>  (int)str_replace(["Rp", " ", "."], "", $request->by_fiducia),
            ];

            DB::transaction(function () use ($enc, $data, $data2) {
                DB::table('a_administrasi')->where('pengajuan_kode', $enc)->update($data);
                DB::table('a_memorandum')->where('pengajuan_kode', $enc)->update($data2);
            });

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }
}

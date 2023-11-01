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

            $adm = ($cek[0]->plafon * $cek[0]->admin) / 100;
            $cek[0]->administrasi = (int)$adm;
            //cek data ada atau tidak
            $administrasi = DB::table('a_administrasi')->where('pengajuan_kode', $enc)->first();

            //Biaya Provisi
            $provisi = ($cek[0]->plafon * $cek[0]->b_provisi) / 100;
            $cek[0]->provisi = (int)$provisi;

            if (is_null($administrasi)) {
                return view('staff.analisa.administrasi', [
                    'data' => $cek[0],
                ]);
            }

            // dd($provisi);
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

            DB::table('a_administrasi')->insert($data);
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

            DB::table('a_administrasi')->where('pengajuan_kode', $enc)->update($data);

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }
}

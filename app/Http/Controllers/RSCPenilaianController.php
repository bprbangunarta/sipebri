<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPenilaianController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
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

            $penilaian = DB::table('rsc_kondisi_usaha')->where('kode_rsc', $enc_rsc)->first();
            $agunan = DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->first();

            return view('rsc.penilaian_debitur.index', [
                'data' => $data[0],
                'penilaian' => $penilaian,
                'agunan' => $agunan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_kondisi_usaha(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $data = [
                'kode_rsc' => $enc_rsc,
                'faktor_teknis1' => $request->faktor_teknis1,
                'faktor_teknis2' => $request->faktor_teknis2,
                'catatan_faktor_teknis' => Str::upper($request->catatan_faktor_teknis),
                'faktor_ekonomi1' => $request->faktor_ekonomi1,
                'faktor_ekonomi2' => $request->faktor_ekonomi2,
                'nominal_fe1' => (int)str_replace(["Rp.", " ", "."], "", $request->nominal_faktor_ekonomi1 ?? 0),
                'nominal_fe2' => (int)str_replace(["Rp.", " ", "."], "", $request->nominal_faktor_ekonomi2 ?? 0),
                'catatan_faktor_ekonomi' => Str::upper($request->catatan_faktor_ekonomi),
                'faktor_marketing1' => $request->faktor_marketing1,
                'faktor_marketing2' => $request->faktor_marketing2,
                'faktor_marketing3' => $request->faktor_marketing3,
                'catatan_faktor_marketing2' => Str::upper($request->catatan_faktor_marketing2),
                'catatan_faktor_marketing3' => Str::upper($request->catatan_faktor_marketing3),
                'catatan_faktor_marketing_lain' => Str::upper($request->catatan_faktor_marketing),
                'faktor_rumah_tangga' => Str::upper($request->faktor_rumah_tangga),
                'biaya_faktor_rumah_tangga' => (int)str_replace(["Rp.", " ", "."], "", $request->biaya_rumah_tangga ?? 0),
                'catatan_frt' => Str::upper($request->catatan_faktor_rumah_tangga),
                'faktor_lainnya' => Str::upper($request->faktor_lain),
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_kondisi_usaha')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_kondisi_usaha(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $data = [
                'kode_rsc' => $enc_rsc,
                'faktor_teknis1' => $request->faktor_teknis1,
                'faktor_teknis2' => $request->faktor_teknis2,
                'catatan_faktor_teknis' => Str::upper($request->catatan_faktor_teknis),
                'faktor_ekonomi1' => $request->faktor_ekonomi1,
                'faktor_ekonomi2' => $request->faktor_ekonomi2,
                'nominal_fe1' => (int)str_replace(["Rp.", " ", "."], "", $request->nominal_faktor_ekonomi1 ?? 0),
                'nominal_fe2' => (int)str_replace(["Rp.", " ", "."], "", $request->nominal_faktor_ekonomi2 ?? 0),
                'catatan_faktor_ekonomi' => Str::upper($request->catatan_faktor_ekonomi),
                'faktor_marketing1' => $request->faktor_marketing1,
                'faktor_marketing2' => $request->faktor_marketing2,
                'faktor_marketing3' => $request->faktor_marketing3,
                'catatan_faktor_marketing2' => Str::upper($request->catatan_faktor_marketing2),
                'catatan_faktor_marketing3' => Str::upper($request->catatan_faktor_marketing3),
                'catatan_faktor_marketing_lain' => Str::upper($request->catatan_faktor_marketing),
                'faktor_rumah_tangga' => Str::upper($request->faktor_rumah_tangga),
                'biaya_faktor_rumah_tangga' => (int)str_replace(["Rp.", " ", "."], "", $request->biaya_rumah_tangga ?? 0),
                'catatan_frt' => Str::upper($request->catatan_faktor_rumah_tangga),
                'faktor_lainnya' => Str::upper($request->faktor_lain),
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_kondisi_usaha')->where('kode_rsc', $enc_rsc)->first();

            if ($update) {
                DB::table('rsc_kondisi_usaha')->where('kode_rsc', $enc_rsc)->update($data);
                return redirect()->back()->with('success', 'Data berhasil diperbarui.');
            } else {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_kondisi_agunan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $data = [
                'kode_rsc' => $enc_rsc,
                'posisi_agunan' => Str::upper($request->posisi_agunan),
                'kondisi_agunan' => Str::upper($request->kondisi_agunan),
                'nilai_taksasi' => (int)str_replace(["Rp.", " ", "."], "", $request->nilai_agunan ?? 0),
                'created_at' => now(),
            ];

            $cek = DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->first();

            if (is_null($cek)) {
                DB::table('rsc_agunan')->insert($data);
                return redirect()->back()->with('success', 'Data berhasil diperbarui.');
            } else {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_kondisi_agunan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $data = [
                'kode_rsc' => $enc_rsc,
                'posisi_agunan' => Str::upper($request->posisi_agunan),
                'kondisi_agunan' => Str::upper($request->kondisi_agunan),
                'nilai_taksasi' => (int)str_replace(["Rp.", " ", "."], "", $request->nilai_agunan ?? 0),
                'updated_at' => now(),
            ];

            $cek = DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->first();

            if (!is_null($cek)) {
                DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->update($data);
                return redirect()->back()->with('success', 'Data berhasil diperbarui.');
            } else {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

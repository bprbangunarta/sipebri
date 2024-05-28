<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPengusulanController extends Controller
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

            $rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $enc)->where('kode_rsc', $enc_rsc)->first();
            $keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();

            // dd($data, $rsc, $keuangan);
            return view('rsc.pengusulan.index', [
                'data' => $data[0],
                'pengusulan' => $rsc,
                'keuangan' => $keuangan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_pengusulan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $cek = $request->validate([
                'jangka_waktu' => 'required',
                'jangka_pokok' => 'required',
                'jangka_bunga' => 'required',
                'suku_bunga' => 'required',
                'metode_rps' => 'required',
                'angsuran_pokok' => 'required',
                'angsuran_bunga' => 'required',
                'total_angsuran' => 'required',
                'angsuran_pokok' => 'required',
                'rc' => 'required',
            ]);

            $data = [
                'jangka_waktu' => $request->jangka_waktu,
                'jangka_pokok' => $request->jangka_pokok,
                'jangka_bunga' => $request->jangka_bunga,
                'suku_bunga' => $request->suku_bunga,
                'metode_rps' => $request->metode_rps,
                'angsuran_pokok' => (int)str_replace(["Rp", " ", "."], "", $request->angsuran_pokok),
                'angsuran_bunga' => (int)str_replace(["Rp", " ", "."], "", $request->angsuran_bunga),
                'total_angsuran' => (int)str_replace(["Rp", " ", "."], "", $request->total_angsuran),
                'rc' => str_replace('%', '', $request->rc),
                'updated_at' => now(),
            ];

            $cek = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->first();

            if (is_null($cek)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

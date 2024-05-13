<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPerdaganganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')
                ->paginate(10);
            //
            $perdagangan = DB::table('rsc_au_perdagangan')->where('kode_rsc', $enc_rsc)->get();

            foreach ($perdagangan as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
            }

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            return view('rsc.usaha_perdagangan.index', [
                'data' => $data[0],
                'perdagangan' => $perdagangan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_perdagangan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $kode_usaha = $this->kodeacak('RSC', 6);

            $data = [
                'kode_rsc' => $enc_rsc,
                'kode_usaha' => $kode_usaha,
                'nama_usaha' => $request->nama_usaha,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_au_perdagangan')->insert($data);
            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_rsc_perdagangan_identitas(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('kode'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
                ->get();

            $pengajuan = DB::table('rsc_au_perdagangan')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_au_perdagangan.kode_rsc')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_au_perdagangan.kode_usaha',
                    'rsc_au_perdagangan.nama_usaha',
                    'rsc_au_perdagangan.lokasi_usaha',
                    'rsc_au_perdagangan.lama_usaha',
                    'data_nasabah.alamat_ktp'
                )
                ->where('kode_usaha', $enc_rsc)->get();
            //
            dd($data, $pengajuan);
            return view('rsc.usaha_perdagangan.identitas', [
                'data' => $data[0],
                // 'perdagangan' => $perdagangan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    private function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('rsc_au_perdagangan')->where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }
        return null;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class DroppingController extends Controller
{
    public function data_cif()
    {
        $keyword = request('keyword');
        $data = DB::table('send_cif')

            ->where(function ($query) use ($keyword) {
                $query->where('tgl_daftar', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_panjang', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('tempat_lahir', 'like', '%' . $keyword . '%')
                    ->orWhere('tanggal_lahir', 'like', '%' . $keyword . '%')
                    ->orWhere('no_id', 'like', '%' . $keyword . '%')
                    ->orWhere('jt_tempo_id', 'like', '%' . $keyword . '%');
            })
            ->orderBy('tgl_daftar', 'desc')
            ->paginate(10);
        return view('dropping.cif', [
            'data' => $data,
        ]);
    }

    public function data_agunan()
    {
        $keyword = request('keyword');
        $data = DB::table('send_jaminan')

            ->where(function ($query) use ($keyword) {
                $query->where('tgl_daftar', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $keyword . '%')
                    ->orWhere('no_spk', 'like', '%' . $keyword . '%')
                    ->orWhere('jenis_agunan', 'like', '%' . $keyword . '%')
                    ->orWhere('no_dokumen', 'like', '%' . $keyword . '%')
                    ->orWhere('lokasi', 'like', '%' . $keyword . '%');
            })
            ->orderBy('tgl_daftar', 'desc')
            ->paginate(10);
        return view('dropping.agunan', [
            'data' => $data,
        ]);
    }

    public function data_kredit()
    {
        $keyword = request('keyword');
        $data = DB::table('send_kredit')

            ->where(function ($query) use ($keyword) {
                $query->where('tgl_daftar', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $keyword . '%')
                    ->orWhere('wilayah', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_produk', 'like', '%' . $keyword . '%')
                    ->orWhere('plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('no_spk', 'like', '%' . $keyword . '%')
                    ->orWhere('jangka_waktu', 'like', '%' . $keyword . '%')
                    ->orWhere('rate_bunga', 'like', '%' . $keyword . '%')
                    ->orWhere('metode', 'like', '%' . $keyword . '%')
                    ->orWhere('tgl_akhir', 'like', '%' . $keyword . '%');
            })
            ->orderBy('tgl_daftar', 'desc')
            ->paginate(10);
        return view('dropping.kredit', [
            'data' => $data,
        ]);
    }
}

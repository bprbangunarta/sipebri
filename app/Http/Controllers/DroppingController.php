<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

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
        //
        // dd($data);
        return view('dropping.kredit', [
            'data' => $data,
        ]);
    }

    public function hapus_spk($pengajuan)
    {
        try {
            $data_spk = DB::table('data_spk')->where('pengajuan_kode', $pengajuan)->first();

            if (!is_null($data_spk)) {
                $data = [
                    'pengajuan_kode' => substr_replace($pengajuan, "XX", 0, 2),
                    'updated_at' => now(),
                ];
                $data2 = [
                    'on_current' => 0,
                ];
                $data3 = [
                    'on_current' => 0,
                ];

                $agunan = DB::table('data_jaminan')->where('pengajuan_kode', $pengajuan)->get();
                if (count($agunan) != 0) {
                    foreach ($agunan as $item) {
                        DB::table('data_jaminan')->where('id', $item->id)->update($data3);
                    }
                }

                // dd($data_spk->id, $data, $agunan, $pengajuan);
                DB::transaction(function () use ($data, $data2, $pengajuan, $data_spk) {
                    Pengajuan::where('kode_pengajuan', $pengajuan)->update($data2);
                    DB::table('data_spk')->where('id', $data_spk->id)->update($data);
                });
                return redirect()->back()->with('success', 'Berhasil Hapus Perjanjian Kredit');
            } else {
                return redirect()->back()->with('error', 'Data Tidak Ada');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal Hapus Perjanjian Kredit');
        }
    }
}

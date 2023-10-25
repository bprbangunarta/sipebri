<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgunanController extends Controller
{
    public function tambah_kendaraan(Request $request)
    {
        try {
            $cek = $request->validate([
                'pengajuan_kode' => 'required',
                'jenis_agunan_kode' => 'required',
                'jenis_jaminan' => 'required',
                'jenis_dokumen_kode' => 'required',
                'no_dokumen' => 'required',
                'atas_nama' => 'required',
                'no_mesin' => 'required',
                'no_polisi' => 'required',
                'no_rangka' => 'required',
                'tipe_kendaraan' => 'required',
                'merek' => 'required',
                'tahun' => 'required',
                'warna' => 'required',
                'kode_dati' => 'required',
                'lokasi' => '',
                'catatan' => '',
                'input_user' => 'required',
            ]);
            $cek['is_entry'] = 1;
            $cek['created_at'] = now();

            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
    public function edit_kendaraan(Request $request)
    {
        //
    }
    public function update_kendaraan(Request $request)
    {
        //
    }

    public function tambah_tanah(Request $request)
    {

        try {
            $cek = $request->validate([
                'pengajuan_kode' => 'required',
                'jenis_jaminan' => 'required',
                'jenis_agunan_kode' => 'required',
                'jenis_dokumen_kode' => 'required',
                'no_dokumen' => 'required',
                'atas_nama' => 'required',
                'luas' => 'required',
                'lokasi' => 'required',
                'input_user' => 'required',
            ]);

            $cek['is_entry'] = 1;
            $cek['luas'] = str_replace('.', '', $request->luas);
            $cek['created_at'] = now();
            // dd($cek);
            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
    public function edit_tanah(Request $request)
    {
        //
    }
    public function update_tanah(Request $request)
    {
        //
    }

    public function tambah_lain(Request $request)
    {
        try {
            $cek = $request->validate([
                'pengajuan_kode' => 'required',
                'jenis_jaminan' => 'required',
                'jenis_agunan_kode' => 'required',
                'jenis_dokumen_kode' => 'required',
                'no_dokumen' => 'required',
                'atas_nama' => 'required',
                'lokasi' => '',
                'catatan' => '',
                'input_user' => 'required',
            ]);

            $cek['is_entry'] = 1;
            $cek['created_at'] = now();
            // dd($cek);
            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
    public function edit_lain(Request $request)
    {
        //
    }
    public function update_lain(Request $request)
    {
        //
    }
}

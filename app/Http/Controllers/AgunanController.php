<?php

namespace App\Http\Controllers;

use App\Models\Agunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    public function edit_agunan($id)
    {
        $data = DB::table('data_jaminan')
            ->leftJoin('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
            ->leftJoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->select('data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen', 'data_jaminan.*')
            ->where('data_jaminan.id', $id)->get();
        //
        //Data dati
        $dati = DB::table('v_dati')
            ->select('nama_dati')
            ->distinct()
            ->where('kode_dati', $data[0]->kode_dati)->get();
        $data[0]->nama_dati = $dati[0]->nama_dati;

        return response()->json($data[0]);
    }
    public function update_kendaraan(Request $request)
    {

        try {
            $data = [
                'jenis_jaminan' => $request->jenis_jaminan,
                'jenis_agunan_kode' => $request->jenis_agunan_kode,
                'jenis_dokumen_kode' => $request->jenis_dokumen_kode,
                'no_dokumen' => $request->no_dokumen,
                'atas_nama' => $request->atas_nama,
                'no_mesin' => $request->no_mesin,
                'no_polisi' => $request->no_polisi,
                'no_rangka' => $request->no_rangka,
                'no_polisi' => $request->no_polisi,
                'tipe_kendaraan' => $request->tipe_kendaraan,
                'merek' => $request->merek,
                'tahun' => $request->tahun,
                'warna' => $request->warna,
                'lokasi' => $request->lokasi,
                'kode_dati' => $request->kode_dati,
                'catatan' => $request->catatan,
                'input_user' => Auth::user()->code_user,
            ];

            $data['is_entry'] = 1;
            $data['otorisasi'] = 'N';
            $data['updated_at'] = now();

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
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

    public function update_tanah(Request $request)
    {

        try {
            $data = [
                'jenis_jaminan' => $request->jenis_jaminan,
                'jenis_agunan_kode' => $request->jenis_agunan_kode,
                'jenis_dokumen_kode' => $request->jenis_dokumen_kode,
                'no_dokumen' => $request->no_dokumen,
                'atas_nama' => $request->atas_nama,
                'lokasi' => $request->lokasi,
                'kode_dati' => $request->kode_dati,
                'input_user' => $request->input_user,
                'catatan' => $request->catatan,
            ];

            $data['is_entry'] = 1;
            $data['otorisasi'] = 'N';
            $data['luas'] = str_replace('.', '', $request->luas);
            $data['updated_at'] = now();

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function edit_agunan_tanah($id)
    {
        $data = DB::table('data_jaminan')
            ->leftJoin('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
            ->leftJoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->select('data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen', 'data_jaminan.*')
            ->where('data_jaminan.id', $id)->get();
        //
        //Data dati
        $dati = DB::table('v_dati')
            ->select('nama_dati')
            ->distinct()
            ->where('kode_dati', $data[0]->kode_dati)->get();
        $data[0]->nama_dati = $dati[0]->nama_dati ?? null;

        return response()->json($data[0]);
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
            $data['otorisasi'] = 'N';
            $cek['created_at'] = now();
            // dd($cek);
            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function edit_agunan_lain($id)
    {
        $data = DB::table('data_jaminan')
            ->leftJoin('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
            ->leftJoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->select('data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen', 'data_jaminan.*')
            ->where('data_jaminan.id', $id)->get();
        //
        return response()->json($data[0]);
    }

    public function update_lain(Request $request)
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
            $cek['otorisasi'] = 'N';
            $cek['created_at'] = now();

            DB::table('data_jaminan')->where('id', $request->id)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
}

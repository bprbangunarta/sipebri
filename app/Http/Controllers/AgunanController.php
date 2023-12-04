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
            $cek['atas_nama'] = strtoupper($request->atas_nama);
            $cek['merek'] = strtoupper($request->merek);
            $cek['is_entry'] = 1;
            $cek['created_at'] = now();

            //Cek data kendaraan
            if ($request->jenis_agunan_kode == '02') {
                $jenis_agunan = 'KENDARAAN RODA 2';
            } elseif ($request->jenis_agunan_kode == '03') {
                $jenis_agunan = 'KENDARAAN RODA 4';
            } elseif (is_null($request->jenis_agunan_kode)) {
                $jenis_agunan = null;
            }

            $cek['catatan'] = 'BPKB' . ',' . ' ' . $jenis_agunan . ',' . ' ' . strtoupper($request->merek) . ',' . ' ' . strtoupper($request->tipe_kendaraan) . ',' . ' ' . strtoupper($request->no_rangka) . ',' . ' ' . strtoupper($request->no_mesin) . ',' . ' ' . strtoupper($request->no_polisi) . ',' . ' ' . strtoupper($request->no_dokumen) . ',' . ' ' . strtoupper($request->warna) . ',' . ' ' . strtoupper($request->atas_nama) . ',' . ' ' . strtoupper($request->lokasi);

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
        //Agunan Kendaraan
        $jenis_kendaraan = DB::table('ja_kendaraan')->get();
        $data_kendaraan = DB::table('da_kendaraan')->get();

        //Data dati
        $dati = DB::table('v_dati')
            ->select('nama_dati')
            ->distinct()
            ->where('kode_dati', $data[0]->kode_dati)->get();
        $data[0]->nama_dati = $dati[0]->nama_dati;

        return response()->json([$data[0], $jenis_kendaraan, $data_kendaraan]);
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
            //Cek data kendaraan
            if ($request->jenis_agunan_kode == '02') {
                $jenis_agunan = 'KENDARAAN RODA 2';
            } elseif ($request->jenis_agunan_kode == '03') {
                $jenis_agunan = 'KENDARAAN RODA 4';
            } elseif (is_null($request->jenis_agunan_kode)) {
                $jenis_agunan = null;
            }

            $cek['catatan'] = 'BPKB' . ',' . ' ' . $jenis_agunan . ',' . ' ' . strtoupper($request->merek) . ',' . ' ' . strtoupper($request->tipe_kendaraan) . ',' . ' ' . strtoupper($request->no_rangka) . ',' . ' ' . strtoupper($request->no_mesin) . ',' . ' ' . strtoupper($request->no_polisi) . ',' . ' ' . strtoupper($request->no_dokumen) . ',' . ' ' . strtoupper($request->warna) . ',' . ' ' . strtoupper($request->atas_nama) . ',' . ' ' . strtoupper($request->lokasi);

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
                'kode_dati' => 'required',
                'lokasi' => 'required',
                'input_user' => 'required',
            ]);

            $cek['is_entry'] = 1;
            $cek['luas'] = str_replace('.', '', $request->luas);
            $cek['created_at'] = now();
            if ($request) {
                $data_catatan = DB::table('data_jenis_agunan')->where('kode', $cek['jenis_agunan_kode'])->first();
                if ($data_catatan->jenis_agunan == 'Tanah') {
                    $jenis_agunan = 'SERTIFIKAT TANAH';
                } else {
                    $jenis_agunan = $data_catatan->jenis_agunan;
                }
            }
            $cek['catatan'] = strtoupper($jenis_agunan) . ' ' . 'NO' . ' ' . strtoupper($cek['no_dokumen']) . ',' . ' ' . 'LUAS' . ' ' . strtoupper($cek['luas']) . ' ' . 'M2' . ',' . ' ' . 'ATAS NAMA' . ' ' . strtoupper($cek['atas_nama']) . ',' . ' ' . 'ALAMAT' . ' ' . strtoupper($cek['lokasi']);
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
        //Agunan Tanah
        $jenis_tanah = DB::table('ja_tanah')->get();
        $data_tanah = DB::table('da_tanah')->get();
        $dati = DB::table('v_dati')->where('kode_dati', $data[0]->kode_dati)->first();
        $data[0]->nama_dati = $dati->nama_dati;

        return response()->json([$data[0], $jenis_tanah, $data_tanah]);
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

            if ($request) {
                $data_catatan = DB::table('data_jenis_agunan')->where('kode', $cek['jenis_agunan_kode'])->first();
                if ($data_catatan->jenis_agunan == 'Kartu Jamsostek') {
                    $jenis_agunan = 'KARTU DAN SALDO JAMSOSTEK';
                } else {
                    $jenis_agunan = $data_catatan->jenis_agunan;
                }
            }
            $cek['catatan'] = strtoupper($jenis_agunan) . ' ' . 'ATAS NAMA' . ' ' . strtoupper($cek['atas_nama']) . ' ' . 'NO' . ' ' . strtoupper($cek['no_dokumen']) . ' ' . 'ALAMAT' . ' ' . strtoupper($cek['lokasi']);

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
        //Agunan Lain
        $jenis_lain = DB::table('ja_lainnya')->get();
        $data_lain = DB::table('da_lainnya')->get();

        return response()->json([$data[0], $jenis_lain, $data_lain]);
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
            if ($request) {
                $data_catatan = DB::table('data_jenis_agunan')->where('kode', $cek['jenis_agunan_kode'])->first();
                if ($data_catatan->jenis_agunan == 'Kartu Jamsostek') {
                    $jenis_agunan = 'KARTU DAN SALDO JAMSOSTEK';
                } else {
                    $jenis_agunan = $data_catatan->jenis_agunan;
                }
            }
            $cek['catatan'] = strtoupper($jenis_agunan) . ' ' . 'ATAS NAMA' . ' ' . strtoupper($cek['atas_nama']) . ' ' . 'NO' . ' ' . strtoupper($cek['no_dokumen']) . ' ' . 'ALAMAT' . ' ' . strtoupper($cek['lokasi']);

            DB::table('data_jaminan')->where('id', $request->id)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function analis_simpan_kendaraan(Request $request)
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
            ]);

            $cek['atas_nama'] = strtoupper($request->atas_nama);
            $cek['merek'] = strtoupper($request->merek);
            $cek['is_entry'] = 1;
            $cek['otorisasi'] = 'A';
            $cek['input_user'] = Auth::user()->code_user;
            $cek['created_at'] = now();
            // dd($request);
            //Cek data kendaraan
            if ($request->jenis_agunan_kode == '02') {
                $jenis_agunan = 'Kendaraan Bermotor Roda 2';
                $jenis_agunan_catatan = 'KENDARAAN RODA 2';
            } elseif ($request->jenis_agunan_kode == '03') {
                $jenis_agunan = 'Kendaraan Bermotor Roda 4';
                $jenis_agunan_catatan = 'KENDARAAN RODA 4';
            } elseif (is_null($request->jenis_agunan_kode)) {
                $jenis_agunan = null;
            }

            $cek['catatan'] = 'BPKB' . ',' . ' ' . $jenis_agunan_catatan . ',' . ' ' . strtoupper($request->merek) . ',' . ' ' . strtoupper($request->tipe_kendaraan) . ',' . ' ' . strtoupper($request->no_rangka) . ',' . ' ' . strtoupper($request->no_mesin) . ',' . ' ' . strtoupper($request->no_polisi) . ',' . ' ' . strtoupper($request->no_dokumen) . ',' . ' ' . strtoupper($request->warna) . ',' . ' ' . strtoupper($request->atas_nama) . ',' . ' ' . strtoupper($request->lokasi);

            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function analis_simpan_tanah(Request $request)
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
                'kode_dati' => 'required',
                'lokasi' => 'required',
            ]);

            $cek['is_entry'] = 1;
            $cek['luas'] = str_replace('.', '', $request->luas);
            $cek['created_at'] = now();
            $cek['otorisasi'] = 'A';
            $cek['input_user'] = Auth::user()->code_user;
            if ($request) {
                $data_catatan = DB::table('data_jenis_agunan')->where('kode', $cek['jenis_agunan_kode'])->first();
                if ($data_catatan->jenis_agunan == 'Tanah') {
                    $jenis_agunan = 'SERTIFIKAT TANAH';
                } else {
                    $jenis_agunan = $data_catatan->jenis_agunan;
                }
            }
            $cek['catatan'] = strtoupper($jenis_agunan) . ' ' . 'NO' . ' ' . strtoupper($cek['no_dokumen']) . ',' . ' ' . 'LUAS' . ' ' . strtoupper($cek['luas']) . ' ' . 'M2' . ',' . ' ' . 'ATAS NAMA' . ' ' . strtoupper($cek['atas_nama']) . ',' . ' ' . 'ALAMAT' . ' ' . strtoupper($cek['lokasi']);

            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function analis_simpan_lain(Request $request)
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
            ]);

            $cek['is_entry'] = 1;
            $cek['otorisasi'] = 'A';
            $cek['input_user'] = Auth::user()->code_user;
            $cek['created_at'] = now();

            if ($request) {
                $data_catatan = DB::table('data_jenis_agunan')->where('kode', $cek['jenis_agunan_kode'])->first();
                if ($data_catatan->jenis_agunan == 'Kartu Jamsostek') {
                    $jenis_agunan = 'KARTU DAN SALDO JAMSOSTEK';
                } else {
                    $jenis_agunan = $data_catatan->jenis_agunan;
                }
            }
            $cek['catatan'] = strtoupper($jenis_agunan) . ' ' . 'ATAS NAMA' . ' ' . strtoupper($cek['atas_nama']) . ' ' . 'NO' . ' ' . strtoupper($cek['no_dokumen']) . ' ' . 'ALAMAT' . ' ' . strtoupper($cek['lokasi']);

            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }
}

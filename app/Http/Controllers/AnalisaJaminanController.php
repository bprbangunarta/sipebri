<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use App\Models\Agunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaJaminanController extends Controller
{
    public function kendaraan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = DB::table('data_pengajuan')
                ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.*', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                ->orWhere('data_jaminan.jenis_jaminan', '=', 'Kendaraan')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();

            //
            //Agunan Kendaraan
            $jenis_kendaraan = DB::table('ja_kendaraan')->get();
            $data_kendaraan = DB::table('da_kendaraan')->get();

            //Agunan Tanah
            $jenis_tanah = DB::table('ja_tanah')->get();
            $data_tanah = DB::table('da_tanah')->get();

            //Agunan Lain
            $jenis_lain = DB::table('ja_lainnya')->get();
            $data_lain = DB::table('da_lainnya')->get();

            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');

            // dd($au);
            return view('staff.analisa.jaminan.kendaraan', [
                'jenis_kendaraan' => $jenis_kendaraan,
                'data_kendaraan' => $data_kendaraan,
                'jenis_tanah' => $jenis_tanah,
                'data_tanah' => $data_tanah,
                'jenis_lain' => $jenis_lain,
                'data_lain' => $data_lain,
                'data' => $cek[0],
                'jaminan' => $au,
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function editkendaraan($id)
    {
        $data = DB::table('data_jaminan')
            ->where('id', $id)
            ->get();

        //Ambil data agunan
        $agunan = Agunan::where('kode', $data[0]->jenis_agunan_kode)->get();

        //Ambil data dokumen
        $dok = DB::table('data_jenis_dokumen')
            ->select('jenis_dokumen')
            ->where('kode', $data[0]->jenis_dokumen_kode)->get();

        /* Menambahkan field baru ke variable data dari data agunan dan data dokumen */
        $data[0]->jenis_agunan = $agunan[0]->jenis_agunan;
        $data[0]->jenis_dokumen = $dok[0]->jenis_dokumen;
        // $data[0]->nama_dati = $dati[0]->nama_dati;

        $data[0]->auth = Auth::user()->code_user;

        $data_agunan = [
            'id' => $data[0]->id,
            'jenis_agunan' => $data[0]->jenis_agunan,
            'jenis_dokumen' => $data[0]->jenis_dokumen,
            'no_dokumen' => $data[0]->no_dokumen,
            'atas_nama' => $data[0]->atas_nama,
            'no_mesin' => $data[0]->no_mesin ?? null,
            'no_polisi' => $data[0]->no_polisi ?? null,
            'no_rangka' => $data[0]->no_rangka ?? null,
            'tipe_kendaraan' => $data[0]->tipe_kendaraan ?? null,
            'merek_kendaraan' => $data[0]->merek ?? null,
            'tahun_kendaraan' => $data[0]->tahun ?? null,
            'warna_kendaraan' => $data[0]->warna ?? null,
            'lokasi_kendaraan' => $data[0]->lokasi,
            'nilai_pasar' => $data[0]->nilai_pasar,
            'nilai_taksasi' => $data[0]->nilai_taksasi,
        ];

        return response()->json($data_agunan);
    }

    public function updatekendaraan(Request $request)
    {
        try {
            $nilai = [
                'nilai_pasar' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nilai_pasar')) ?? 0,
                'nilai_taksasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nilai_taksasi')) ?? 0,
                'updated_at' => now(),
            ];
            // dd($request);
            // $cek['catatan'] = 'BPKB' . '-' . $jenis_agunan . '-' . strtoupper($request->merek) . '-' . $request->tipe_kendaraan . '-' . $request->no_rangka . '-' . $request->no_mesin . '-' . $request->no_polisi . '-' . $request->no_dokumen . '-' . $request->warna . '-' . strtoupper($request->atas_nama) . '-' . $request->lokasi;
            DB::table('data_jaminan')->where('id', $request->id)->update($nilai);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('errors', 'Gagal menambahkan data');
    }

    public function previewkendaraan(Request $request)
    {

        $id = $request->input('iddata');
        $no = $request->input('no');
        $jaminan = DB::table('data_jaminan')->where('id', $id)->first();
        if (is_null($jaminan->$no)) {
            $foto = null;
        } else {
            $foto = $jaminan->foto1 ? asset('storage/image/photo_agunan/' . $jaminan->$no) : null;
            // $foto = $jaminan->foto1 ? asset('storage/image/photo_agunan/' . $jaminan->$no) : null;
        }

        return response()->json($foto);

        // return view('tampilkan_gambar', ['image' => $base64Image]);
    }

    public function fhotokendaraan(Request $request)
    {
        try {
            $cek = $request->validate([
                'foto1' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto2' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto3' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto4' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto5' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto6' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $tanggalSekarang = Carbon::now();
            $tanggal = $tanggalSekarang->format('dmY');

            if ($request->file('foto1')) {
                if ($request->name_img_1) {
                    Storage::delete('public/image/photo_agunan/' . $request->name_img_1);
                }

                $ekstensi = $cek['foto1']->getClientOriginalExtension();
                $new1 =  $request->jenis . '_' . 'depan' . '_' . $tanggal .  '_' . $request->nama . '.' . $ekstensi;
                $cek['foto1'] = $request->file('foto1')->storeAs('image/photo_agunan', $new1, 'public');
                $cek['foto1'] = $new1;
            } else {
                $cek['foto1'] = $request->name_img_1;
            }

            if ($request->file('foto2')) {
                if ($request->name_img_2) {
                    Storage::delete('public/image/photo_agunan/' . $request->name_img_2);
                }
                $ekstensi = $cek['foto2']->getClientOriginalExtension();
                $new2 =  $request->jenis . '_' . 'belakang' . '_' . $tanggal .  '_' . $request->nama . '.' . $ekstensi;
                $cek['foto2'] = $request->file('foto2')->storeAs('image/photo_agunan', $new2, 'public');
                $cek['foto2'] = $new2;
            } else {
                $cek['foto2'] = $request->name_img_2;
            }

            if ($request->file('foto3')) {
                if ($request->name_img_3) {
                    Storage::delete('public/image/photo_agunan/' . $request->name_img_3);
                }
                $ekstensi = $cek['foto3']->getClientOriginalExtension();
                $new3 =  $request->jenis . '_' . 'kiri' . '_' . $tanggal .  '_' . $request->nama . '.' . $ekstensi;
                $cek['foto3'] = $request->file('foto3')->storeAs('image/photo_agunan', $new3, 'public');
                $cek['foto3'] = $new3;
            } else {
                $cek['foto3'] = $request->name_img_3;
            }

            if ($request->file('foto4')) {
                if ($request->name_img_4) {
                    Storage::delete('public/image/photo_agunan/' . $request->name_img_4);
                }
                $ekstensi = $cek['foto4']->getClientOriginalExtension();
                $new4 =  $request->jenis . '_' . 'kanan' . '_' . $tanggal .  '_' . $request->nama . '.' . $ekstensi;
                $cek['foto4'] = $request->file('foto4')->storeAs('image/photo_agunan', $new4, 'public');
                $cek['foto4'] = $new4;
            } else {
                $cek['foto4'] = $request->name_img_4;
            }

            if ($request->file('foto5')) {
                if ($request->name_img_5) {
                    Storage::delete('public/image/photo_agunan/' . $request->name_img_5);
                }
                $ekstensi = $cek['foto5']->getClientOriginalExtension();
                $new4 =  $request->jenis . '_' . 'lain1' . '_' . $tanggal .  '_' . $request->nama . '.' . $ekstensi;
                $cek['foto5'] = $request->file('foto5')->storeAs('image/photo_agunan', $new4, 'public');
                $cek['foto5'] = $new4;
            } else {
                $cek['foto5'] = $request->name_img_5;
            }

            if ($request->file('foto6')) {
                if ($request->name_img_6) {
                    Storage::delete('public/image/photo_agunan/' . $request->name_img_6);
                }
                $ekstensi = $cek['foto6']->getClientOriginalExtension();
                $new4 =  $request->jenis . '_' . 'lain2' . '_' . $tanggal .  '_' . $request->nama . '.' . $ekstensi;
                $cek['foto6'] = $request->file('foto6')->storeAs('image/photo_agunan', $new4, 'public');
                $cek['foto6'] = $new4;
            } else {
                $cek['foto6'] = $request->name_img_6;
            }
            // dd($cek, $request->all());
            DB::table('data_jaminan')->where('id', $request->id)->update($cek);
            return redirect()->back()->with('success', 'Berhasil menambahkan fhoto');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan fhoto');
        }
    }

    public function datakendaraan($dataId)
    {
        $jaminan = DB::table('data_jaminan')->where('id', $dataId)->first();
        return response()->json($jaminan);
    }

    public function tanah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = DB::table('data_pengajuan')
                ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.id', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jaminan.lokasi', 'data_jaminan.luas', 'data_jaminan.otorisasi', 'data_jaminan.nilai_taksasi', 'data_jaminan.nilai_pasar', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                ->orWhere('data_jaminan.jenis_jaminan', '=', 'Tanah')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            //
            //Agunan Kendaraan
            $jenis_kendaraan = DB::table('ja_kendaraan')->get();
            $data_kendaraan = DB::table('da_kendaraan')->get();

            //Agunan Tanah
            $jenis_tanah = DB::table('ja_tanah')->get();
            $data_tanah = DB::table('da_tanah')->get();

            //Agunan Lain
            $jenis_lain = DB::table('ja_lainnya')->get();
            $data_lain = DB::table('da_lainnya')->get();

            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');

            return view('staff.analisa.jaminan.tanah', [
                'jenis_kendaraan' => $jenis_kendaraan,
                'data_kendaraan' => $data_kendaraan,
                'jenis_tanah' => $jenis_tanah,
                'data_tanah' => $data_tanah,
                'jenis_lain' => $jenis_lain,
                'data_lain' => $data_lain,
                'data' => $cek[0],
                'jaminan' => $au,
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function edittanah($id)
    {
        $tanah = DB::table('data_jaminan')
            ->leftJoin('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
            ->leftJoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->where('data_jaminan.id', $id)->first();
        //

        $tanah->jaminan_id = $id;
        return response()->json($tanah);
    }

    public function simpantanah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'nilai_pasar' => (int)str_replace(["Rp.", " ", "."], "", $request->nilai_pasar) ?? 0,
                'nilai_taksasi' => (int)str_replace(["Rp.", " ", "."], "", $request->nilai_taksasi) ?? 0,
                'updated_at' => now(),
            ];

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Berhasil melakukan perubahan');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal melakukan perubahan');
    }

    public function lain(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = DB::table('data_pengajuan')
                ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.id', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jaminan.lokasi', 'data_jaminan.luas', 'data_jaminan.catatan', 'data_jaminan.otorisasi', 'data_jaminan.nilai_taksasi', 'data_jaminan.nilai_pasar', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                ->orWhere('data_jaminan.jenis_jaminan', '=', 'Lainnya')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            // dd($au);

            //Agunan Lain
            $jenis_lain = DB::table('ja_lainnya')->get();
            $data_lain = DB::table('da_lainnya')->get();
            $usr = Auth::user()->code_user;

            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');
            return view('staff.analisa.jaminan.lainnya', [
                'jenis_lain' => $jenis_lain,
                'data_lain' => $data_lain,
                'data' => $cek[0],
                'jaminan' => $au,
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function editlain($dataId)
    {
        $lain = DB::table('data_jaminan')
            ->leftJoin('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
            ->leftJoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->where('data_jaminan.id', $dataId)->first();
        //
        $lain->jaminan_id = $dataId;
        return response()->json($lain);
    }

    public function simpanlain(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $data = [
                'nilai_pasar' => (int)str_replace(["Rp.", " ", "."], "", $request->nilai_pasar) ?? 0,
                'nilai_taksasi' => (int)str_replace(["Rp.", " ", "."], "", $request->nilai_taksasi) ?? 0,
                'no_dokumen' => strtoupper($request->no_dok),
                'updated_at' => now(),
                'catatan' => $request->catatan,
            ];

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function delete_lain($id)
    {
        try {
            $data = DB::table('data_jaminan')->where('id', $id)->first();
            if (!is_null($data)) {
                DB::table('data_jaminan')->where('id', $data->id)->delete();
                return response()->json(['message' => 'Data Berhasil Dihapus.']);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Data Gagal Dihapus.']);
        }
    }
}

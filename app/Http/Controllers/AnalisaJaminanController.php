<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use App\Models\Agunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AnalisaJaminanController extends Controller
{
    public function kendaraan(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            // $au = Midle::taksasi_jaminan($enc);
            $au = DB::table('data_pengajuan')
                ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.id', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jaminan.otorisasi', 'data_jaminan.nilai_taksasi', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                ->orWhere('data_jaminan.jenis_jaminan', '=', 'Kendaraan')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            // dd($au);
            return view('staff.analisa.jaminan.kendaraan', [
                'data' => $cek[0],
                'jaminan' => $au,
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
            'merek_kendaraan' => $data[0]->merek_kendaraan ?? null,
            'tahun_kendaraan' => $data[0]->tahun_kendaraan ?? null,
            'warna_kendaraan' => $data[0]->warna_kendaraan ?? null,
            'lokasi_kendaraan' => $data[0]->lokasi,
            'nilai_pasar' => $data[0]->nilai_pasar,
            'nilai_taksasi' => $data[0]->nilai_taksasi,
        ];

        return response()->json($data_agunan);
        // return response()->json([$data, $agn, $dokumen]);
    }

    public function updatekendaraan(Request $request)
    {
        try {
            $nilai = [
                'nilai_pasar' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nilai_pasar')) ?? 0,
                'nilai_taksasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nilai_taksasi')) ?? 0,
                'updated_at' => now(),
            ];
            DB::table('data_jaminan')->where('id', $request->id)->update($nilai);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('succeserrors', 'Gagal menambahkan data');
    }

    public function previewkendaraan($id)
    {
        $jaminan = DB::table('data_jaminan')->where('id', $id)->first();
        $foto1 = asset('storage/image/photo_agunan/' . $jaminan->foto1);
        $foto2 = asset('storage/image/photo_agunan/' . $jaminan->foto2);
        $foto3 = asset('storage/image/photo_agunan/' . $jaminan->foto3);
        $foto4 = asset('storage/image/photo_agunan/' . $jaminan->foto4);
        return response()->json([$foto1, $foto2, $foto3, $foto4]);
    }

    public function fhotokendaraan(Request $request)
    {
        try {
            $cek = $request->validate([
                'foto1' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto2' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto3' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto4' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $tanggalSekarang = Carbon::now();
            $tanggal = $tanggalSekarang->format('dmY');

            if ($request->file('foto1')) {
                $ekstensi = $cek['foto1']->getClientOriginalExtension();
                $new1 = 'depan' . '_' . $tanggal . '.' . $ekstensi;
                $cek['foto1'] = $request->file('foto1')->storeAs('image/photo_agunan', $new1, 'public');
                $cek['foto1'] = $new1;
            }

            if ($request->file('foto2')) {
                $ekstensi = $cek['foto2']->getClientOriginalExtension();
                $new2 = 'belakang' . '_' . $tanggal . '.' . $ekstensi;
                $cek['foto2'] = $request->file('foto2')->storeAs('image/photo_agunan', $new2, 'public');
                $cek['foto2'] = $new2;
            }

            if ($request->file('foto3')) {
                $ekstensi = $cek['foto3']->getClientOriginalExtension();
                $new3 = 'kiri' . '_' . $tanggal . '.' . $ekstensi;
                $cek['foto3'] = $request->file('foto3')->storeAs('image/photo_agunan', $new3, 'public');
                $cek['foto3'] = $new3;
            }

            if ($request->file('foto4')) {
                $ekstensi = $cek['foto4']->getClientOriginalExtension();
                $new4 = 'kanan' . '_' . $tanggal . '.' . $ekstensi;
                $cek['foto4'] = $request->file('foto4')->storeAs('image/photo_agunan', $new4, 'public');
                $cek['foto4'] = $new4;
            }
            DB::table('data_jaminan')->where('id', $request->id)->update($cek);
            return redirect()->back()->with('success', 'Berhasil menambahkan fhoto');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan fhoto');
        }
    }

    public function tanah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            // $au = Midle::taksasi_jaminan($enc);
            $au = DB::table('data_pengajuan')
                ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.id', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jaminan.lokasi', 'data_jaminan.luas', 'data_jaminan.otorisasi', 'data_jaminan.nilai_taksasi', 'data_jaminan.nilai_pasar', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                ->orWhere('data_jaminan.jenis_jaminan', '=', 'Tanah')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            // dd($au);
            return view('staff.analisa.jaminan.tanah', [
                'data' => $cek[0],
                'jaminan' => $au,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function lain(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Midle::taksasi_jaminan($enc);

            return view('staff.analisa.jaminan.lainnya', [
                'data' => $cek[0],
                'jaminan' => $au,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

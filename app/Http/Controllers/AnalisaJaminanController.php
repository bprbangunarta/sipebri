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
            $au = Midle::taksasi_jaminan($enc);
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

    public function fhotokendaraan(Request $request)
    {
        dd($request);
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

    public function tanah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Midle::taksasi_jaminan($enc);

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

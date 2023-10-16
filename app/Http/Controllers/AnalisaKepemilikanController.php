<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Kepemilikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaKepemilikanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            $has = Kepemilikan::where('pengajuan_kode', $enc)->first();

            if (is_null($has)) {
                return view('staff.analisa.kepemilikan', [
                    'data' => $cek[0],
                    'milik' => $has,
                ]);
            }
            // dd($has);
            return view('staff.analisa.kepemilikan-edit', [
                'data' => $cek[0],
                'milik' => $has,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->kode_pengajuan);
            $name = 'HRK';
            $length = 5;
            $kode = Kepemilikan::kodeacak($name, $length);

            $data = [
                'kode_kepemilikan' => $kode,
                'pengajuan_kode' => $enc,
                'rumah' => strtoupper($request->rumah),
                'mobil' => strtoupper($request->mobil),
                'motor' => strtoupper($request->motor),
                'televisi' => strtoupper($request->tv),
                'komputer' => strtoupper($request->komputer),
                'mesin_cuci' => strtoupper($request->mesin_cuci),
                'kursi_tamu' => strtoupper($request->kursi),
                'lemari_panjang' => strtoupper($request->lemari),
                'nama_lainnya1' => strtoupper($request->nama_lain1) ?? null,
                'isi_lainnya1' => strtoupper($request->lainnya1) ?? null,
                'nama_lainnya2' => strtoupper($request->nama_lain2) ?? null,
                'isi_lainnya2' => strtoupper($request->lainnya2) ?? null,
            ];
            // dd($data);
            if ($data) {
                DB::table('data_kepemilikan')->insert($data);
                return redirect()->back()->with('success', 'Harta Kepemilikan berhasil ditambahkan');
            }
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            // dd($enc);
            $data = [
                'rumah' => strtoupper($request->rumah),
                'mobil' => strtoupper($request->mobil),
                'motor' => strtoupper($request->motor),
                'televisi' => strtoupper($request->tv),
                'komputer' => strtoupper($request->komputer),
                'mesin_cuci' => strtoupper($request->mesin_cuci),
                'kursi_tamu' => strtoupper($request->kursi),
                'lemari_panjang' => strtoupper($request->lemari),
                'nama_lainnya1' => strtoupper($request->nama_lain1) ?? null,
                'isi_lainnya1' => strtoupper($request->lainnya1) ?? null,
                'nama_lainnya2' => strtoupper($request->nama_lain2) ?? null,
                'isi_lainnya2' => strtoupper($request->lainnya2) ?? null,
            ];

            if ($data) {
                $pemilik = Kepemilikan::where('pengajuan_kode', $enc)->get();
                DB::table('data_kepemilikan')->where('id', $pemilik[0]->id)->update($data);
                return redirect()->back()->with('success', 'Harta Kepemilikan berhasil ditambahkan')->with('pengajuan', $request->query('pengajuan'));;
            }
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Jasa;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UsahaJasaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Jasa::au_jasa($enc);

            foreach ($au as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
                $item->kode_id = Crypt::encrypt($item->id);
            }
            // dd($au);
            return view('staff.analisa.u-jasa.index', [
                'data' => $cek[0],
                'jasa' => $au
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {
        $req = $request->query('pengajuan');
        try {
            $enc = Crypt::decrypt($req);
            $name = 'AUJ';
            $length = 5;
            $kode = Jasa::kodeacak($name, $length);

            if ($kode !== null) {
                $data = $request->validate([
                    'nama_usaha' => 'required'
                ]);
                $data['kode_usaha'] = $kode;
                $data['pengajuan_kode'] = $enc;
                $data['input_user'] = Auth::user()->code_user;
                $data['nama_usaha'] = ucwords($data['nama_usaha']); //Kapital depannya saja

                try {
                    Jasa::create($data);
                    return redirect()->back()->with('success', 'Nama usaha berhasil ditambahkan');
                } catch (Throwable $th) {
                    return redirect()->back()->with('error', 'Nama usaha gagal ditambahkan');
                }
            } else {
                $kode = Jasa::kodeacak($name, $length);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function keuangan(Request $request)
    {
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));
            $kode = Crypt::decrypt($request->query('kode_usaha'));
            $cek = Midle::analisa_usaha($encpengajuan);

            $jasa = Jasa::where('kode_usaha', $kode)->first();
            $jasa->kd_usaha = Crypt::encrypt($jasa->kode_usaha);
            $jasa->kd_pengajuan = $request->query('pengajuan');
            // dd($jasa);
            return view('staff.analisa.u-jasa.keuangan', [
                'data' => $cek[0],
                'jasa' => $jasa,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpankeuangan(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $cek = $request->validate([
                'kode_usaha' => 'required',
                'nama_usaha' => 'required',
                'lokasi_usaha' => 'required',
                'lama_usaha' => 'required',
                'pendapatan' => '',
                'b_pajak' => '',
                'b_lainnya' => '',
                'pengeluaran' => '',
                'laba_bersih' => '',
            ]);

            $cek['kode_usaha'] = $enc;
            $cek['nama_usaha'] = ucwords($request->nama_usaha);
            $cek['lokasi_usaha'] = ucwords($request->lokasi_usaha);
            $cek['lama_usaha'] = ucwords($request->lama_usaha);
            $cek['pendapatan'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan'));
            $cek['b_pajak'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('b_pajak'));
            $cek['b_lainnya'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('b_lainnya'));
            $cek['pengeluaran'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran'));
            $cek['laba_bersih'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih'));
            $cek['input_user'] = Auth::user()->code_user;

            Jasa::where('kode_usaha', $enc)->update($cek);
            return redirect()->back()->with('success', 'Data usaha jasa berhasil ditambahkan');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data usaha jasa gagal ditambahkan');
    }
}

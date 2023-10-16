<?php

namespace App\Http\Controllers;

use App\Models\Lain;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UsahaLainnyaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Lain::index_lain($enc);

            foreach ($au as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }

            return view('staff.analisa.u-lainnya.index', [
                'data' => $cek[0],
                'lain' => $au,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpanlain(Request $request)
    {
        try {
            $req = $request->query('pengajuan');
            $enc = Crypt::decrypt($req);
            $name = 'AUL';
            $length = 5;
            $kode = Lain::kodeacak($name, $length);

            if ($kode !== null) {
                $data = $request->validate([
                    'nama_usaha' => 'required',
                    'jenis_usaha' => 'required',
                ]);
                $data['kode_usaha'] = $kode;
                $data['pengajuan_kode'] = $enc;
                $data['input_user'] = Auth::user()->code_user;
                $data['nama_usaha'] = ucwords($data['nama_usaha']); //Kapital depannya saja
                $data['jenis_usaha'] = ucwords($data['jenis_usaha']); //Kapital depannya saja

                try {
                    Lain::create($data);
                    return redirect()->back()->with('success', 'Nama usaha berhasil ditambahkan');
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Nama usaha gagal ditambahkan');
                }
            } else {
                $kode = Lain::kodeacak($name, $length);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function identitas(Request $request)
    {
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data USaha Lainnya
            $enc = Crypt::decrypt($request->query('kode_usaha'));

            $lain = Lain::where('kode_usaha', $enc)->first();
            $lain->kd_usaha = Crypt::encrypt($lain->kode_usaha);
            // dd($lain);
            return view('staff.analisa.u-lainnya.identitas', [
                'data' => $cek[0],
                'lain' => $lain,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpanidentitas(Request $request)
    {
        $req = $request->validate([
            'lama_usaha' => 'required',
            'lokasi_usaha' => 'required',
        ]);

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            Lain::where('kode_usaha', $enc)->update($req);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function keuangan(Request $request)
    {

        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data keuangan
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $lain = Lain::where('kode_usaha', $enc)->first();
            $lain->kd_usaha = Crypt::encrypt($lain->kode_usaha);
            // dd($lain);
            return view('staff.analisa.u-lainnya.keuangan', [
                'data' => $cek[0],
                'lain' => $lain,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

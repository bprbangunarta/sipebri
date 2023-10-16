<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AnalisaJaminanController extends Controller
{
    public function kendaraan(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Midle::taksasi_jaminan($enc);

            return view('staff.analisa.jaminan.kendaraan', [
                'data' => $cek[0],
                'jaminan' => $au,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpankendaraan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            // dd($request);
            // $data = [
            //     'pengajuan_kode' => $enc,
            //     'pengajuan_kode' => $enc,
            // ];
            // DB::table('data_jaminan')-
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function tanah(Request $request)
    {
        dd($request);
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
}

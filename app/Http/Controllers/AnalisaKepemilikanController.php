<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalisaKepemilikanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            $has = Kepemilikan::where('pengajuan_kode', $enc)->first();

            return view('staff.analisa.kepemilikan', [
                'data' => $cek[0],
                'milik' => $has,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

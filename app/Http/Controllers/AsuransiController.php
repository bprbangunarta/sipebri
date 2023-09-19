<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AsuransiController extends Controller
{
    public function index(Request $request)
    {   
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));                   
            $cek = Midle::analisa_usaha($enc);
            $data = (object) [
                'jenis_tanggungan' => 'Motor',
                'tgl_lahir' => '12-01-1994',
                'no_polisi' => '123456789',
                'tgl_realisasi' => Carbon::now()->format('d-m-Y'),
                'jangka_waktu' => '36',
            ];
            // dd($data);
            return view('analisa.asuransi', [
                'data' => $cek[0],
                'asuransi' => $data,
            ]);

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function edit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));       
            $cek = Midle::analisa_usaha($enc);
            
            // dd($data);
            return view('analisa.asuransi-edit', [
                'data' => $cek[0],
            ]);

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $tracking = DB::table('data_tracking')->where('pengajuan_kode', $enc)->first();

            return view('tracking.pengajuan', [
                'data' => $tracking,
            ]);
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

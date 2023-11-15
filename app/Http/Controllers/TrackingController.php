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
            $tracking = DB::table('data_tracking')
                ->leftJoin('data_pengajuan', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->where('pengajuan_kode', $enc)
                ->select(
                    'data_pengajuan.*',
                    'data_tracking.*',
                )->first();

            // Validasi data
            if (is_null($tracking)) {
                return redirect()->back()->with('error', 'Data Pengajuan Belum Lengakap');
            }

            return view('tracking.pengajuan', [
                'data' => $tracking,
            ]);
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

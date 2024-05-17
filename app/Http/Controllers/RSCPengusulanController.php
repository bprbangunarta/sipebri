<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPengusulanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_telp',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                    'data_spk.no_spk',
                    'data_spk.created_at',
                    'data_spk.updated_at',
                )
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->get();
            //
            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            $rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $enc)->where('kode_rsc', $enc_rsc)->first();

            // dd($data, $rsc);
            return view('rsc.pengusulan.index', [
                'data' => $data[0],
                'pengusulan' => $rsc
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}

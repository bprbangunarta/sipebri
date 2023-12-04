<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FrontController extends Controller
{
    public function index()
    {
        return view('front-end.index');
    }

    public function pengajuan()
    {
        return view('front-end.pengajuan');
    }

    public function tracking()
    {
        return view('front-end.tracking');
    }

    public function verifikasi(Request $request)
    {
        $req = $request->query('qrcode');
        $img = str_replace('"', '', $req);

        $array = (object)explode('_', $img);
        $data_nasabah = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->leftJoin('data_usulan', 'data_usulan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_usulan.input_user')
            ->leftJoin('users', 'users.code_user', '=', 'data_usulan.input_user')
            ->select(
                'data_nasabah.nama_nasabah',
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.jangka_waktu',
                'data_usulan.role_name',
                'data_notifikasi.created_at as tanggal_notifikasi',
                'data_tracking.analisa_kredit as analisa_kredit',
                'v_users.nama_user',
                'users.ttd',
                'data_produk.kode_produk',
                'data_produk.nama_produk',
                'data_usulan.usulan_plafon',
                'data_usulan.suku_bunga',
                'data_usulan.metode_rps',
                'data_usulan.b_provisi',
                'data_usulan.b_admin',
                'data_usulan.rc',
            )
            ->where('data_pengajuan.kode_pengajuan', '=', $array->{1})
            ->where('data_usulan.pengajuan_kode', '=', $array->{1})
            ->where('data_usulan.input_user', '=', $array->{2})->get()->last();
        //
        // $hari = Carbon::now();
        $data_nasabah->analisa_kredit = Carbon::parse($data_nasabah->analisa_kredit)->translatedFormat('d F Y');

        return view('front-end.qr-code', [
            'data' => $data_nasabah,
        ]);
    }
}

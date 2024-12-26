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

    public function informasi_tracking()
    {
        try {
            $kode = request()->input('kode');

            if (empty($kode)) {
                abort('404', 'Data tidak ditemukan.');
            }

            DB::statement("SET lc_time_names = 'id_ID'");
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_berkas', 'data_berkas.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
                ->leftJoin('data_kantor as dari_kantor', 'dari_kantor.kode_kantor', '=', 'data_berkas.dari_kantor')
                ->leftJoin('data_kantor as ke_kantor', 'ke_kantor.kode_kantor', '=', 'data_berkas.ke_kantor')
                ->leftJoin('v_users as pengirim', 'pengirim.code_user', '=', 'data_berkas.user_pengirim')
                ->leftJoin('v_users as penerima', 'penerima.code_user', '=', 'data_berkas.user_penerima')
                ->leftJoin('v_users as tujuan', 'tujuan.code_user', '=', 'data_berkas.user_tujuan')
                ->select(
                    'data_pengajuan.kode_pengajuan',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.jangka_waktu',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'pengirim.nama_user as user_pengirim',
                    'penerima.nama_user as user_penerima',
                    'tujuan.nama_user as users_tujuan',
                    'data_berkas.user_tujuan',
                    'dari_kantor.nama_kantor as dari_kantor',
                    'ke_kantor.nama_kantor as ke_kantor',
                    'data_produk.nama_produk',
                    DB::raw("DATE_FORMAT(data_berkas.tgl_kirim, '%d-%m-%Y') as tgl_kirim"),
                    DB::raw("DATE_FORMAT(data_berkas.tgl_terima, '%d-%m-%Y') as tgl_terima"),
                    DB::raw("DATE_FORMAT(STR_TO_DATE(data_nasabah.tanggal_lahir, '%Y%m%d'), '%d %M %Y') as tgl_lahir")
                )
                ->where('data_pengajuan.kode_pengajuan', $kode)
                ->first();

            return view('front-end.informasi_tracking', compact('data'));
        } catch (\Throwable $th) {
            abort('404', 'Data tidak ditemukan.');
        }
    }

    public function monitoring_tracking()
    {
        $kode = request()->input('kode');
        if (empty($kode)) {
            abort('404', 'Data tidak ditemukan.');
        }

        $data = DB::table('data_pengajuan')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->select(
                'data_pengajuan.status',
                'data_pengajuan.kode_pengajuan',
                'data_tracking.pemeriksaan_dokumen',
                'data_tracking.proses_survey',
                'data_tracking.analisa_kredit',
                'data_tracking.keputusan_komite',
                'data_tracking.akad_kredit',
            )
            ->where('data_pengajuan.kode_pengajuan', $kode)
            ->first();
        // dd($data);
        return view('front-end.monitoring_tracking', compact('data'));
    }

    public function verifikasi(Request $request)
    {
        $req = $request->query('qrcode');
        $img = str_replace('"', '', $req);

        $array = (object)explode('_', $img);

        if ($array->{0} == 'Analisa Kredit') {
            return self::verifikasi_analisa($array);
        } elseif ($array->{0} == 'Penolakan Kredit') {
            return self::verifikasi_penolakan($array);
        }

        $data_nasabah = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->leftJoin('data_usulan', 'data_usulan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
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
        $data_nasabah->tanggal_notifikasi = Carbon::parse($data_nasabah->tanggal_notifikasi)->translatedFormat('d F Y');
        return view('front-end.qr-code', [
            'data' => $data_nasabah,
        ]);
    }

    public function verifikasi_analisa($arr)
    {
        // dd($arr);
        $data_nasabah = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->leftJoin('data_usulan', 'data_usulan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_usulan.input_user')
            ->leftJoin('users', 'users.code_user', '=', 'data_usulan.input_user')
            ->select(
                'data_nasabah.nama_nasabah',
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.jangka_waktu',
                'data_usulan.role_name',
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
                'data_usulan.created_at as tanggal_notifikasi',
            )
            ->where('data_pengajuan.kode_pengajuan', '=', $arr->{1})
            ->where('data_usulan.pengajuan_kode', '=', $arr->{1})
            ->where('data_usulan.input_user', '=', $arr->{2})->get()->last();
        //
        // $hari = Carbon::now();
        $data_nasabah->tanggal_notifikasi = Carbon::parse($data_nasabah->tanggal_notifikasi)->translatedFormat('d F Y');
        return view('front-end.qr-code', [
            'data' => $data_nasabah,
        ]);
    }

    public function verifikasi_penolakan($arr)
    {

        // return redirect()->back()->with('error', 'Data sedang dalam perbaikan');
        // $data_nasabah = DB::table('data_pengajuan')
        //     ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
        //     ->leftJoin('data_usulan', 'data_usulan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
        //     ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
        //     ->leftJoin('v_users', 'v_users.code_user', '=', 'data_usulan.input_user')
        //     ->leftJoin('users', 'users.code_user', '=', 'data_usulan.input_user')
        //     ->select(
        //         'data_nasabah.nama_nasabah',
        //         'data_pengajuan.kode_pengajuan',
        //         'data_pengajuan.jangka_waktu',
        //         'data_usulan.role_name',
        //         'v_users.nama_user',
        //         'users.ttd',
        //         'data_produk.kode_produk',
        //         'data_produk.nama_produk',
        //         'data_usulan.usulan_plafon',
        //         'data_usulan.suku_bunga',
        //         'data_usulan.metode_rps',
        //         'data_usulan.b_provisi',
        //         'data_usulan.b_admin',
        //         'data_usulan.rc',
        //         'data_usulan.created_at as tanggal_notifikasi',
        //     )
        //     ->where('data_pengajuan.kode_pengajuan', '=', $arr->{1})
        //     ->where('data_usulan.pengajuan_kode', '=', $arr->{1})
        //     ->where('data_usulan.input_user', '=', $arr->{2})->get()->last();
        // //

        // $data_nasabah->tanggal_notifikasi = Carbon::parse($data_nasabah->tanggal_notifikasi)->translatedFormat('d F Y');
        // return view('front-end.qr-code', [
        //     'data' => $data_nasabah,
        // ]);
    }
}

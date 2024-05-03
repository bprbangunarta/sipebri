<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class CetakLaporanController extends Controller
{
    public function laporan_pendaftaran()
    {
        $keyword = request('keyword');
        $produk = request('kode_produk');
        $kantor = request('nama_kantor');
        $metode = request('metode');
        $surveyor = request('surveyor');
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.metode_rps',
                'data_nasabah.no_telp',
                'data_pendamping.nama_pendamping',
                'v_users.nama_user',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.jangka_waktu', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.suku_bunga', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.metode_rps', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.no_telp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pendamping.nama_pendamping', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%');
            });

        if ($tgl1 !== null) {
            $query->where(function ($query) use ($tgl1, $tgl2) {
                $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            });
        }

        $query->where(function ($query) use ($produk, $kantor, $metode, $surveyor) {
            $query->where('data_produk.kode_produk', 'like', '%' . $produk . '%')
                ->where('data_survei.surveyor_kode', 'like', '%' . $surveyor . '%')
                ->where('data_pengajuan.metode_rps', 'like', '%' . $metode . '%')
                ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');
        })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);

        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();

        return view('laporan.pendaftaran', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'surveyor' => $surveyor,
        ]);
    }

    public function laporan_sebelum_survey()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.kasi_kode')

            ->whereIn('data_pengajuan.tracking', ['Verifikasi Data', 'Penjadwalan', 'Proses Survei'])

            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_nasabah.no_telp',
                'data_survei.tgl_survei',
                'v_users.nama_user',
                'data_pengajuan.tracking',
                'data_survei.catatan_survei',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.no_telp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.tgl_survei', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.tracking', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.catatan_survei', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_pengajuan.created_at', 'desc');

        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();

        $data = $query->paginate(10);
        return view('laporan.sebelum-survey', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'surveyor' => $surveyor,
        ]);
    }

    public function laporan_sesudah_survey()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')

            ->whereIn('data_pengajuan.tracking', ['Proses Analisa', 'Persetujuan Komite', 'Naik Kasi', 'Naik Komite I', 'Naik Komite II'])

            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_nasabah.no_telp',
                'data_survei.tgl_survei',
                'v_users.nama_user',
                'data_pengajuan.tracking',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.no_telp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.tgl_survei', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.tracking', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_pengajuan.created_at', 'desc');

        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();

        $data = $query->paginate(10);
        return view('laporan.sesudah-survey', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'surveyor' => $surveyor,
        ]);
    }

    public function laporan_penolakan()
    {
        $keyword = request('keyword');
        $produk = request('kode_produk');
        $kantor = request('nama_kantor');
        $metode = request('metode');
        $surveyor = request('surveyor');
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.kasi_kode')
            ->leftJoin('data_penolakan', 'data_penolakan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_alasan_penolakan', 'data_alasan_penolakan.id', '=', 'data_penolakan.alasan_id')
            ->select(
                'data_tracking.keputusan_komite as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'v_users.nama_user as komite',
                'v_users.role_name as jabatan',
                'data_penolakan.no_penolakan',
                'data_penolakan.keterangan as keterangan_tolak',
                'data_alasan_penolakan.alasan as alasan_tolak',
            )
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Dibatalkan')
                    ->orWhere('data_pengajuan.status', '=', 'Ditolak');
            })

            ->where(function ($query) use ($keyword) {
                $query->where('data_tracking.keputusan_komite', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.role_name', 'like', '%' . $keyword . '%')
                    ->orWhere('data_penolakan.no_penolakan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_penolakan.keterangan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_alasan_penolakan.alasan', 'like', '%' . $keyword . '%');
            });

        if ($tgl1 !== null) {
            $query->where(function ($query) use ($tgl1, $tgl2) {
                $query->whereBetween('data_tracking.keputusan_komite', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            });
        }

        $query->where(function ($query) use ($produk, $kantor, $metode, $surveyor) {
            $query->where('data_produk.kode_produk', 'like', '%' . $produk . '%')
                ->where('data_survei.surveyor_kode', 'like', '%' . $surveyor . '%')
                ->where('data_pengajuan.metode_rps', 'like', '%' . $metode . '%')
                ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');
        })
            ->orderBy('data_tracking.keputusan_komite', 'desc');
        $data = $query->paginate(10);

        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();

        // dd($data);
        return view('laporan.penolakan', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'surveyor' => $surveyor,
        ]);
    }

    public function pengajuan_disetujui(Request $request)
    {
        $keyword = request('keyword');
        $produk = request('kode_produk');
        $kantor = request('nama_kantor');
        $metode = request('metode');
        $surveyor = request('surveyor');
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->leftJoin('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')

            ->where('data_pengajuan.on_current', 0)
            ->where('data_pengajuan.status', 'Disetujui')
            ->whereNull('data_notifikasi.no_notifikasi')

            ->select(
                'data_tracking.keputusan_komite as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.metode_rps',
                'data_nasabah.no_telp',
                'data_pendamping.nama_pendamping',
                'v_users.nama_user',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.jangka_waktu', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.suku_bunga', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.metode_rps', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.no_telp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pendamping.nama_pendamping', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%');
            });

        if ($tgl1 !== null) {
            $query->where(function ($query) use ($tgl1, $tgl2) {
                $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            });
        }

        $query->where(function ($query) use ($produk, $kantor, $metode, $surveyor) {
            $query->where('data_produk.kode_produk', 'like', '%' . $produk . '%')
                ->where('data_survei.surveyor_kode', 'like', '%' . $surveyor . '%')
                ->where('data_pengajuan.metode_rps', 'like', '%' . $metode . '%')
                ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');
        })

            ->orderBy('data_tracking.keputusan_komite', 'desc');
        $data = $query->paginate(10);

        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();

        return view('laporan.pengajuan-disetujui', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'surveyor' => $surveyor,
        ]);
    }

    public function siap_realisasi()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')

            ->where('data_pengajuan.on_current', 0)
            ->where('data_pengajuan.status', 'Disetujui')
            // ->whereNotNull('data_notifikasi.keterangan')
            ->whereNull('data_spk.no_spk')

            ->select(
                'data_notifikasi.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'v_users.nama_user',
                'data_notifikasi.no_notifikasi',
                'data_notifikasi.keterangan',
                'data_notifikasi.rencana_realisasi as rencana',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_notifikasi.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_notifikasi.no_notifikasi', 'like', '%' . $keyword . '%')
                    ->orWhere('data_notifikasi.keterangan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_notifikasi.rencana_realisasi', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_notifikasi.created_at', 'desc');
        $data = $query->paginate(10);
        return view('laporan.siap-realisasi', [
            'data' => $data,
        ]);
    }

    public function laporan_pencairan()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->where('data_pengajuan.on_current', 1)

            ->select(
                'data_spk.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_spk.no_spk',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.metode_rps',
                // Jth. Tempo dibuat di blade, kalau bisa pindahkan ke controller
                'v_users.nama_user',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_spk.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_spk.no_spk', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.jangka_waktu', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.suku_bunga', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.metode_rps', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_spk.created_at', 'desc');

        // $kantor = DB::table('data_kantor')->get();
        // $produk = DB::table('data_produk')->get();
        // $metode = DB::table('data_metode_rps')->get();
        // $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();
        // $cgc = DB::table('v_tabungan')->get();
        // $resort = DB::table('v_resort')->get();

        $data = $query->paginate(10);
        return view('laporan.realisasi', [
            'data' => $data,
            // 'kantor' => $kantor,
            // 'produk' => $produk,
            // 'metode' => $metode,
            // 'surveyor' => $surveyor,
            // 'cgc' => $cgc,
            // 'resort' => $resort,
        ]);
    }

    public function laporan_tracking_pengajuan()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
            ->leftjoin('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')

            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.tracking',
                'v_users.nama_user',
                'data_survei.tgl_survei as tgl_survey',
                'data_tracking.analisa_kredit as tgl_analisa',
                'data_tracking.keputusan_komite as tgl_persetujuan',
                'data_tracking.akad_kredit as tgl_realisasi',
                'data_pengajuan.status',
                'data_notifikasi.created_at as tgl_notif',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.jangka_waktu', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.suku_bunga', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.tgl_survei', 'like', '%' . $keyword . '%')
                    ->orWhere('data_tracking.analisa_kredit', 'like', '%' . $keyword . '%')
                    ->orWhere('data_tracking.keputusan_komite', 'like', '%' . $keyword . '%')
                    ->orWhere('data_tracking.akad_kredit', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.status', 'like', '%' . $keyword . '%');
            })
            ->whereNot(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Batal');
            })

            ->orderBy('data_pengajuan.created_at', 'desc');

        $data = $query->paginate(10);
        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Resort
        $resort = DB::table('v_resort')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();
        // dd($data);
        return view('laporan.tracking-pengajuan', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'resort' => $resort,
            'surveyor' => $surveyor,
        ]);
    }

    public function laporan_fasilitas()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_spk.*',
                'data_survei.*',
                'data_tracking.*',
            )
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.on_current', 1)

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_tracking.akad_kredit', 'desc');

        //Data Kantor
        $kantor = DB::table('data_kantor')->get();
        //Data Produk
        $produk = DB::table('data_produk')->get();
        //Data Metode RPS
        $metode = DB::table('data_metode_rps')->get();
        //Data Surveyor
        $surveyor = DB::table('v_users')->where('role_name', 'Staff Analis')->get();
        //Data CGC
        $cgc = DB::table('v_tabungan')->get();
        //Data CGC
        $resort = DB::table('v_resort')->get();

        $data = $query->paginate(10);
        return view('laporan.realisasi', [
            'data' => $data,
            'kantor' => $kantor,
            'produk' => $produk,
            'metode' => $metode,
            'surveyor' => $surveyor,
            'cgc' => $cgc,
            'resort' => $resort,
        ]);
    }
    public function post_laporan_fasilitas()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.on_current', 1)

            ->when(
                $tgl1 && $tgl2,
                function ($query) use ($tgl1, $tgl2) {
                    return $query->whereBetween('data_tracking.akad_kredit', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
                }
            )

            ->orderBy('data_tracking.akad_kredit', 'desc');
        $data = $query->paginate(10);

        return view('laporan.fasilitas', [
            'data' => $data,
        ]);
    }


    public function post_laporan_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->join('data_tracking', 'data_pengajuan.kode_pengajuan', '=', 'data_tracking.pengajuan_kode')
            // ->where(function ($query) {
            //     $query->where('data_pengajuan.status', '=', 'Disetujui')
            //         ->where('data_spk.pengajuan_kode', '!=', null);
            // })

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_tracking.pencairan_dana', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_tracking.*',
            )
            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);

        return view('laporan.realisasi', [
            'data' => $data,
        ]);
    }


    public function post_siap_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_notifikasi.no_notifikasi',
                'data_notifikasi.created_at as tanggal',
                'data_notifikasi.keterangan',
                'data_notifikasi.rencana_realisasi',
                'data_survei.kantor_kode as wilayah',
                'users.name as surveyor',
            )
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')
            ->where('data_pengajuan.on_current', '0')

            ->when(
                $tgl1 && $tgl2,
                function ($query) use ($tgl1, $tgl2) {
                    return $query->whereBetween('data_notifikasi.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
                }
            )

            ->orderBy('data_notifikasi.created_at', 'desc');
        $data = $query->paginate(10);
        return view('laporan.siap-realisasi', [
            'data' => $data,
        ]);
    }



    public function laporan_survey_analisa(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.tracking', [
                'Penjadwalan',
                'Proses Survei',
                'Proses Analisa',
                'Naik Kasi',
                'Naik Komite I',
                'Naik Komite II'
            ])

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )
            ->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(10);

        return view('laporan.survey-analisa', [
            'data' => $data,
        ]);
    }

    public function post_laporan_survey(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.tracking', [
                'Penjadwalan',
                'Proses Survei',
                'Proses Analisa',
                'Naik Kasi',
                'Naik Komite I',
                'Naik Komite II'
            ])

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )
            ->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(7);

        return view('laporan.survey-analisa', [
            'data' => $data,
        ]);
    }

    public function post_laporan_penolakan(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Dibatalkan')
                    ->orWhere('data_pengajuan.status', '=', 'Ditolak');
            })

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(7);

        return view('laporan.penolakan', [
            'data' => $data,
        ]);
    }

    public function laporan_notifikasi(Request $request)
    {
        $name = request('name');

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_notifikasi.created_at as tgl_notifikasi',
                'data_pengajuan.kode_pengajuan',
                'data_notifikasi.no_notifikasi',
                'data_nasabah.nama_nasabah',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'users.name as surveyor',
            )
            ->orderBy('data_pengajuan.created_at', 'desc');

        $data = $query->paginate(10);
        return view('laporan.rekap-notifikasi', [
            'data' => $data,
        ]);
    }
}

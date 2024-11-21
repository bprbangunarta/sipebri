<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringStaffController extends Controller
{
    public function index()
    {
        $data = DB::table('v_users')
            ->leftJoin('data_survei', 'data_survei.surveyor_kode', '=', 'v_users.code_user')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->select(
                'v_users.nama_user',
                'v_users.code_user',
                DB::raw("SUM(CASE WHEN data_pengajuan.tracking = 'Proses Survei' THEN 1 ELSE 0 END) as total_survei"),
                DB::raw("SUM(CASE WHEN data_pengajuan.tracking = 'Proses Analisa' THEN 1 ELSE 0 END) as total_analisa"),
                DB::raw("SUM(CASE WHEN data_pengajuan.tracking = 'Naik Kasi' THEN 1 ELSE 0 END) as total_naik_kasi"),
                DB::raw("SUM(CASE WHEN data_pengajuan.status = 'Ditolak' THEN 1 ELSE 0 END) as total_tolak"),
                DB::raw("SUM(CASE WHEN data_pengajuan.status = 'Dibatalkan' THEN 1 ELSE 0 END) as total_batal")
            )
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->groupBy('v_users.nama_user')
            ->orderBy('v_users.nama_user', 'ASC')
            ->paginate(10);

        //

        return view('analisa.monitoring.index', compact('data'));
    }

    public function detail(Request $request)
    {
        try {
            $keyword = request('keyword');
            $user = request('user');
            $status = strtoupper(request('status'));
            $name = request('name');

            $data = DB::table('data_pengajuan')
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
                ->where('data_survei.surveyor_kode', $user)
                ->where('data_pengajuan.tracking', $status)
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
                ->paginate(10);
            //

            return view('analisa.monitoring.detail', compact('status', 'user', 'data', 'name'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    }

    public function detail_status(Request $request)
    {
        try {
            $keyword = request('keyword');
            $user = request('user');
            $status = strtoupper(request('trc'));
            $trc = strtoupper(request('trc'));
            $name = request('name');

            $data = DB::table('data_pengajuan')
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
                ->where('data_survei.surveyor_kode', $user)
                ->where('data_pengajuan.status', $status)
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
                ->paginate(10);
            //

            return view('analisa.monitoring.detail_status', compact('status', 'trc', 'user', 'data', 'name'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    }
}

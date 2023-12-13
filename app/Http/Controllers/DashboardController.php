<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Survei;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pengajuan = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->count();

        $sebelum_survey = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.kasi_kode')
            ->whereIn('data_pengajuan.tracking', ['Verifikasi Data', 'Penjadwalan', 'Proses Survey'])
            ->count();

        $sesudah_survey = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
            ->whereIn('data_pengajuan.tracking', ['Proses Analisa', 'Persetujuan Komite', 'Naik Kasi', 'Naik Komite I', 'Naik Komite II'])
            ->count();

        $pencairan = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->where('data_pengajuan.on_current', 1)
            ->count();

        $siap_realisasi = DB::table('data_pengajuan')
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
        ->whereNotNull('data_notifikasi.keterangan')
        ->whereNull('data_spk.no_spk')
        ->count();

        $penolakan = Pengajuan::where('status', 'Ditolak')
            ->orWhere('status', 'Dibatalkan')
            ->orWhere('status', 'Batal')
            ->count();

        $keyword = request('keyword');
        $query = DB::table('data_droping')
            ->where('tgl_perjanjian', '=', date('Y-m-d'))
            ->where('droping', '=', 1)

            ->where(function ($query) use ($keyword) {
                $query->where('nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('wilayah', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('tgl_perjanjian', 'desc');
        $dt = $query->get();
        $tak = [];
        for ($i = 0; $i < count($dt); $i++) {
            $tak[] = $dt[$i]->plafon ?? 0;
        }
        $total = array_sum($tak);
        $realisasi = $query->paginate(10);
        $survei = $sebelum_survey + $sesudah_survey;

        return view('dashboard', [
            'pengajuan' => $pengajuan,
            'survei' => $survei,
            'disetujui' => $pencairan,
            'siap_realisasi' => $siap_realisasi,
            'penolakan' => $penolakan,
            'data' => $realisasi,
            'total' => $total,
        ]);
    }
}

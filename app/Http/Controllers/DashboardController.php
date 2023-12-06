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
        $survei = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.tracking', [
                'Penjadwalan',
                'Proses Survei',
                'Proses Analisa',
                'Naik Kasi',
                'Naik Komite I',
                'Naik Komite II'
            ])
           ->count();

        $disetujui = Pengajuan::where('status', 'Disetujui')
            ->where('on_current', '1')
            ->count();

        $siap_realisasi = DB::table('data_pengajuan')
        ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
        ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
        ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
        ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
        ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')
        ->where('data_pengajuan.on_current', '0')
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
        $pengajuan = $survei + $siap_realisasi + $disetujui;

        return view('dashboard', [
            'pengajuan' => $pengajuan,
            'survei' => $survei,
            'disetujui' => $disetujui,
            'siap_realisasi' => $siap_realisasi,
            'penolakan' => $penolakan,
            'data' => $realisasi,
            'total' => $total,
        ]);
    }
}

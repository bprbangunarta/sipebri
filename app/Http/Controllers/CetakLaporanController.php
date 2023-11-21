<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakLaporanController extends Controller
{
    public function laporan_fasilitas()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Disetujui')
                    ->where('data_pengajuan.on_current', '=', 0);
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);
        // dd($data);
        return view('laporan.fasilitas', [
            'data' => $data,
        ]);
    }

    public function laporan_realisasi()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Disetujui')
                    ->where('data_spk.pengajuan_kode', '!=', null);
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);

        return view('laporan.realisasi', [
            'data' => $data,
        ]);
    }

    public function laporan_penolakan()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Dibatalkan')
                    ->orWhere('data_pengajuan.status', '=', 'Ditolak');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);

        return view('laporan.realisasi', [
            'data' => $data,
        ]);
    }
}
